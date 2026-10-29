output "app_url" {
  description = "Endereco local do TaskFlow"
  value       = "http://localhost:${var.app_port}"
}

output "container_name" {
  description = "Nome do container Docker"
  value       = docker_container.taskflow.name
}

output "volume_name" {
  description = "Volume persistente do SQLite"
  value       = docker_volume.taskflow_data.name
}

output "image_name" {
  description = "Imagem Docker utilizada"
  value       = docker_image.taskflow.name
}
