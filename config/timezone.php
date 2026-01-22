<?php
/**
 * Arquivo de configuração de fuso horário
 * Configurar timezone do PHP para Brasil (São Paulo - UTC-3)
 */

// Define o fuso horário para Brasil (São Paulo)
date_default_timezone_set('America/Sao_Paulo');

/**
 * Observações Importantes:
 * - America/Sao_Paulo: UTC-3 (Brasília, São Paulo, Rio Grande do Sul)
 * - America/Manaus: UTC-4 (Amazonas)
 * - America/Fortaleza: UTC-3 (Nordeste)
 * - America/Recife: UTC-3 (Pernambuco)
 * 
 * Usar 'America/Sao_Paulo' cobre a maioria do Brasil
 * e ajusta automaticamente para horário de verão quando aplicável
 */

// Funções auxiliares para data/hora em PT-BR
function dataAgora() {
    return date('d/m/Y H:i:s');
}

function horaAgora() {
    return date('H:i:s');
}

function dataHoje() {
    return date('Y-m-d'); // Para inputs type="date"
}

function formatarData($data) {
    return date('d/m/Y', strtotime($data));
}

function formatarDataHora($data) {
    return date('d/m/Y H:i', strtotime($data));
}

function formatarDataHoraCompleta($data) {
    return date('d/m/Y H:i:s', strtotime($data));
}
?>
