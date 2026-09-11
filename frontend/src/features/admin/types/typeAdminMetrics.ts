export interface MetricsCounts {
  total: number
  mes: number
  semana: number
}

export interface SystemMetrics {
  acessos: MetricsCounts
  usuarios_ativos: MetricsCounts
}