-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 26-Jan-2026 às 16:36
-- Versão do servidor: 8.0.44-0ubuntu0.22.04.2
-- versão do PHP: 8.1.2-1ubuntu2.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `impressoras_db`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `impressoras`
--

CREATE TABLE `impressoras` (
  `id` int NOT NULL,
  `modelo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_serie` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `localizacao` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('equipamento_completo','equipamento_manutencao','inativo') COLLATE utf8mb4_unicode_ci DEFAULT 'equipamento_completo',
  `contagem_paginas` int DEFAULT '0',
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data/hora de cadastro (UTC-3 Brasil)',
  `ultima_atualizacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última atualização (UTC-3 Brasil)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `impressoras`
--

INSERT INTO `impressoras` (`id`, `modelo`, `marca`, `numero_serie`, `localizacao`, `status`, `contagem_paginas`, `data_cadastro`, `ultima_atualizacao`) VALUES
(1, 'SCX-5637FR', 'SAMSUNG', 'Z5W1BAIB700313L', 'Setor Helpdesk', 'equipamento_completo', 341582, '2026-01-22 17:34:29', '2026-01-22 17:34:29'),
(2, 'ES5112', 'OKIDATA', 'AK5C049536', 'CEMITERIO', 'equipamento_manutencao', 47264, '2026-01-22 18:22:48', '2026-01-22 18:22:48'),
(3, 'teste', 'HP', 'teste341434', 'teste1', 'equipamento_manutencao', 1478, '2026-01-23 17:08:53', '2026-01-23 17:08:53'),
(5, 'teste 1', 'KYOCERA', 'teste341435', 'teste1', 'equipamento_manutencao', 3415, '2026-01-26 17:19:50', '2026-01-26 17:19:50');

-- --------------------------------------------------------

--
-- Estrutura da tabela `pecas_retiradas`
--

CREATE TABLE `pecas_retiradas` (
  `id` int NOT NULL,
  `impressora_id` int NOT NULL,
  `nome_peca` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantidade` int DEFAULT '1',
  `data_retirada` date NOT NULL COMMENT 'Data da retirada da peça (formato: YYYY-MM-DD)',
  `observacao` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `pecas_retiradas`
--

INSERT INTO `pecas_retiradas` (`id`, `impressora_id`, `nome_peca`, `quantidade`, `data_retirada`, `observacao`) VALUES
(1, 1, 'Unidade fusora', 1, '2026-01-22', 'teste');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `impressoras`
--
ALTER TABLE `impressoras`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_serie` (`numero_serie`),
  ADD KEY `idx_marca` (`marca`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_modelo` (`modelo`);

--
-- Índices para tabela `pecas_retiradas`
--
ALTER TABLE `pecas_retiradas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_impressora` (`impressora_id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `impressoras`
--
ALTER TABLE `impressoras`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `pecas_retiradas`
--
ALTER TABLE `pecas_retiradas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `pecas_retiradas`
--
ALTER TABLE `pecas_retiradas`
  ADD CONSTRAINT `pecas_retiradas_ibfk_1` FOREIGN KEY (`impressora_id`) REFERENCES `impressoras` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
