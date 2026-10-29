terraform {
  required_version = ">= 1.5.0"

  required_providers {
    docker = {
      source  = "kreuzwerker/docker"
      version = "4.6.0"
    }
  }
}

provider "docker" {}

# Construir a imagem da aplicação Laravel
resource "docker_image" "taskflow" {
  name         = "taskflow-app:1.0"
  keep_locally = true

  build {
    context    = abspath("${path.module}/..")
    dockerfile = "Dockerfile"
  }

  triggers = {
    source_hash = sha256(join("", [
      for f in sort(concat(
        tolist(fileset("${path.module}/..", "app/**")),
        tolist(fileset("${path.module}/..", "routes/**")),
        tolist(fileset("${path.module}/..", "resources/**")),
        [
          "Dockerfile",
          ".dockerignore",
          "composer.json",
          "composer.lock",
          "package.json",
          "package-lock.json",
          "vite.config.js",
          "scripts/start.sh"
        ]
      )) : filesha256("${path.module}/../${f}")
    ]))
  }
}

# Armazenamento persistente para o SQLite
resource "docker_volume" "taskflow_data" {
  name = "taskflow-data"
}

# Executar a aplicação em um contêiner
resource "docker_container" "taskflow" {
  name    = "taskflow-app"
  image   = docker_image.taskflow.image_id
  restart = "unless-stopped"

  ports {
    internal = 8000
    external = var.app_port
    ip       = "127.0.0.1"
  }

  volumes {
    volume_name    = docker_volume.taskflow_data.name
    container_path = "/data"
  }

  env = [
    "APP_NAME=TaskFlow",
    "APP_ENV=local",
    "APP_DEBUG=true",
    "APP_URL=http://localhost:${var.app_port}",
    "DB_CONNECTION=sqlite",
    "DB_DATABASE=/data/taskflow.sqlite",
    "SESSION_DRIVER=database",
    "CACHE_STORE=database",
    "QUEUE_CONNECTION=database",
    "LOG_CHANNEL=stderr"
  ]
}
