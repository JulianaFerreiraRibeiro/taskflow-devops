variable "app_port" {
  description = "Porta local de acesso ao TaskFlow"
  type        = number
  default     = 8000

  validation {
    condition     = var.app_port >= 1024 && var.app_port <= 65535
    error_message = "A porta deve estar entre 1024 e 65535."
  }
}
