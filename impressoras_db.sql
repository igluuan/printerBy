-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 27-Jan-2026 às 16:11
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
  `numero_serie` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `localizacao` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('equipamento_completo','equipamento_manutencao','inativo') COLLATE utf8mb4_unicode_ci DEFAULT 'equipamento_completo',
  `contagem_paginas` int DEFAULT '0',
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data/hora de cadastro (UTC-3 Brasil)',
  `ultima_atualizacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última atualização (UTC-3 Brasil)',
  `modelo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marca` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_modelo` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `impressoras`
--

INSERT INTO `impressoras` (`id`, `numero_serie`, `localizacao`, `status`, `contagem_paginas`, `data_cadastro`, `ultima_atualizacao`, `modelo`, `marca`, `id_modelo`) VALUES
(8, 'OKI-001-2024', 'Recepção Principal', 'equipamento_completo', 12500, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'ES5112 PN', 'OKIDATA', 3),
(18, 'SAM-003-2023', 'Contabilidade', 'equipamento_completo', 98500, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'ProXpress SL-M4020ND', 'SAMSUNG', 9),
(19, 'SAM-004-2022', 'Suporte Técnico', 'equipamento_manutencao', 187000, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'ML-3710ND', 'SAMSUNG', 11),
(20, 'SAM-005-2024', 'Design', 'equipamento_completo', 24500, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'SL-C4062FX', 'SAMSUNG', 12),
(21, 'SAM-006-2023', 'Colorida - Marketing', 'equipamento_completo', 56700, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'CLX-6260FR', 'SAMSUNG', 13),
(22, 'SAM-007-2022', 'Arquivo', 'inativo', 234100, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'ProXpress M4580FX', 'SAMSUNG', 14),
(23, 'SAM-008-2024', 'Recepção 2º Andar', 'equipamento_completo', 12300, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'SCX-5637', 'SAMSUNG', 10),
(24, 'HP-001-2024', 'Compras', 'equipamento_completo', 43200, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'LaserJet Managed E42540', 'HP', 15),
(25, 'HP-002-2023', 'Vendas', 'equipamento_completo', 87600, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'LaserJet Managed E50145', 'HP', 16),
(26, 'HP-003-2023', 'Logística', 'equipamento_manutencao', 145200, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'LaserJet Managed E60165', 'HP', 17),
(27, 'HP-004-2024', 'Expedição', 'equipamento_completo', 28900, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'LaserJet Managed E55040', 'HP', 18),
(28, 'HP-005-2022', 'Qualidade', 'inativo', 198700, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'Laser 432fdn', 'HP', 19),
(29, 'HP-006-2024', 'Portaria', 'equipamento_completo', 5600, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'Laser 408dn', 'HP', 20),
(30, 'BRO-001-2024', 'Administrativo', 'equipamento_completo', 67800, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'MFC-L6902DW', 'BROTHER', 23),
(31, 'BRO-002-2023', 'Produção', 'equipamento_completo', 123400, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'HL-L6202DW', 'BROTHER', 25),
(32, 'BRO-003-2023', 'Engenharia', 'equipamento_manutencao', 98700, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'MFC-6702DW', 'BROTHER', 27),
(33, 'BRO-004-2024', 'Laboratório', 'equipamento_completo', 34500, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'DCP-L5652DN', 'BROTHER', 28),
(34, 'KYO-001-2023', 'Sala de Copias Central', 'equipamento_completo', 187600, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'TASKalfa 3551ci', 'KYOCERA', 31),
(35, 'KYO-002-2024', 'Biblioteca', 'equipamento_completo', 25600, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'ECOSYS P3155dn', 'KYOCERA', 34),
(36, 'RIC-001-2024', 'Auditoria', 'equipamento_completo', 43200, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'SP C352DN', 'RICOH', 22),
(37, 'XER-001-2023', 'Presidência', 'equipamento_completo', 28900, '2026-01-27 14:35:41', '2026-01-27 14:35:41', 'VersaLink C400', 'XEROX', 35),
(98, 'OKI-101-2024', 'Recepção', 'equipamento_completo', 12500, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'ES5112 PN', 'OKIDATA', 3),
(99, 'OKI-102-2024', 'Financeiro', 'equipamento_completo', 85600, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'ES4172LP MFP', 'OKIDATA', 1),
(100, 'OKI-103-2023', 'RH', 'equipamento_manutencao', 124800, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'ES5162LP MFP', 'OKIDATA', 2),
(101, 'OKI-104-2023', 'TI', 'equipamento_completo', 32000, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'ES6405N', 'OKIDATA', 4),
(102, 'OKI-105-2022', 'Almoxarifado', 'inativo', 215600, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'MC780', 'OKIDATA', 5),
(103, 'OKI-106-2024', 'Marketing', 'equipamento_completo', 45000, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'MPS5502MB', 'OKIDATA', 6),
(104, 'OKI-107-2023', 'Jurídico', 'equipamento_completo', 18900, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'C831N', 'OKIDATA', 7),
(105, 'OKI-108-2024', 'Call Center', 'equipamento_completo', 78000, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'ES5112 PN', 'OKIDATA', 3),
(106, 'SAM-101-2024', 'Reuniões A', 'equipamento_completo', 65400, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'SCX-5637', 'SAMSUNG', 10),
(107, 'SAM-102-2023', 'Diretoria', 'equipamento_completo', 32000, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'ProXpress M4070FR', 'SAMSUNG', 8),
(108, 'SAM-103-2023', 'Contabilidade', 'equipamento_completo', 98500, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'ProXpress SL-M4020ND', 'SAMSUNG', 9),
(109, 'SAM-104-2022', 'Suporte', 'equipamento_manutencao', 187000, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'ML-3710ND', 'SAMSUNG', 11),
(110, 'SAM-105-2024', 'Design', 'equipamento_completo', 24500, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'SL-C4062FX', 'SAMSUNG', 12),
(111, 'SAM-106-2023', 'Marketing Color', 'equipamento_completo', 56700, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'CLX-6260FR', 'SAMSUNG', 13),
(112, 'SAM-107-2022', 'Arquivo', 'inativo', 234100, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'ProXpress M4580FX', 'SAMSUNG', 14),
(113, 'SAM-108-2024', 'Recepção 2º', 'equipamento_completo', 12300, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'SCX-5637', 'SAMSUNG', 10),
(114, 'HP-101-2024', 'Compras', 'equipamento_completo', 43200, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'LaserJet Managed E42540', 'HP', 15),
(115, 'HP-102-2023', 'Vendas', 'equipamento_completo', 87600, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'LaserJet Managed E50145', 'HP', 16),
(116, 'HP-103-2023', 'Logística', 'equipamento_manutencao', 145200, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'LaserJet Managed E60165', 'HP', 17),
(117, 'HP-104-2024', 'Expedição', 'equipamento_completo', 28900, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'LaserJet Managed E55040', 'HP', 18),
(118, 'HP-105-2022', 'Qualidade', 'inativo', 198700, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'Laser 432fdn', 'HP', 19),
(119, 'HP-106-2024', 'Portaria', 'equipamento_completo', 5600, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'Laser 408dn', 'HP', 20),
(120, 'BRO-101-2024', 'Admin', 'equipamento_completo', 67800, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'MFC-L6902DW', 'BROTHER', 23),
(121, 'BRO-102-2023', 'Produção', 'equipamento_completo', 123400, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'HL-L6202DW', 'BROTHER', 25),
(122, 'BRO-103-2023', 'Engenharia', 'equipamento_manutencao', 98700, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'MFC-6702DW', 'BROTHER', 27),
(123, 'BRO-104-2024', 'Laboratório', 'equipamento_completo', 34500, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'DCP-L5652DN', 'BROTHER', 28),
(124, 'KYO-101-2023', 'Cópias', 'equipamento_completo', 187600, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'TASKalfa 3551ci', 'KYOCERA', 31),
(125, 'KYO-102-2024', 'Biblioteca', 'equipamento_completo', 25600, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'ECOSYS P3155dn', 'KYOCERA', 34),
(126, 'RIC-101-2024', 'Auditoria', 'equipamento_completo', 43200, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'SP C352DN', 'RICOH', 22),
(127, 'XER-101-2023', 'Presidência', 'equipamento_completo', 10254, '2026-01-27 14:44:55', '2026-01-27 14:44:55', 'VersaLink C400', 'XEROX', 35),
(128, 'TEST-NOVO-001', 'Teste', 'equipamento_completo', 0, '2026-01-27 14:52:42', '2026-01-27 14:52:42', 'ES5112 PN', 'OKIDATA', 3),
(133, 'TEST-OK-001', 'Teste', 'equipamento_completo', 0, '2026-01-27 14:56:40', '2026-01-27 14:56:40', 'ES5112 PN', 'OKIDATA', 3);

-- --------------------------------------------------------

--
-- Estrutura da tabela `impressoras_backup`
--

CREATE TABLE `impressoras_backup` (
  `id` int NOT NULL DEFAULT '0',
  `numero_serie` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `localizacao` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('equipamento_completo','equipamento_manutencao','inativo') COLLATE utf8mb4_unicode_ci DEFAULT 'equipamento_completo',
  `contagem_paginas` int DEFAULT '0',
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data/hora de cadastro (UTC-3 Brasil)',
  `ultima_atualizacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última atualização (UTC-3 Brasil)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `marcas`
--

CREATE TABLE `marcas` (
  `id_marca` int NOT NULL,
  `nome_marca` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `marcas`
--

INSERT INTO `marcas` (`id_marca`, `nome_marca`) VALUES
(5, 'BROTHER'),
(3, 'HP'),
(6, 'KYOCERA'),
(1, 'OKIDATA'),
(4, 'RICOH'),
(2, 'SAMSUNG'),
(7, 'XEROX');

-- --------------------------------------------------------

--
-- Estrutura da tabela `modelos_conhecidos`
--

CREATE TABLE `modelos_conhecidos` (
  `id_modelo` int NOT NULL,
  `marca` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modelo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `eh_multifuncional` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `modelos_conhecidos`
--

INSERT INTO `modelos_conhecidos` (`id_modelo`, `marca`, `modelo`, `eh_multifuncional`) VALUES
(1, 'OKIDATA', 'ES4172LP MFP', 1),
(2, 'OKIDATA', 'ES5162LP MFP', 1),
(3, 'OKIDATA', 'ES5112 PN', 0),
(4, 'OKIDATA', 'ES6405N', 0),
(5, 'OKIDATA', 'MC780', 1),
(6, 'OKIDATA', 'MPS5502MB', 1),
(7, 'OKIDATA', 'C831N', 0),
(8, 'SAMSUNG', 'ProXpress M4070FR', 1),
(9, 'SAMSUNG', 'ProXpress SL-M4020ND', 0),
(10, 'SAMSUNG', 'SCX-5637', 1),
(11, 'SAMSUNG', 'ML-3710ND', 0),
(12, 'SAMSUNG', 'SL-C4062FX', 1),
(13, 'SAMSUNG', 'CLX-6260FR', 1),
(14, 'SAMSUNG', 'ProXpress M4580FX', 1),
(15, 'HP', 'LaserJet Managed E42540', 1),
(16, 'HP', 'LaserJet Managed E50145', 1),
(17, 'HP', 'LaserJet Managed E60165', 1),
(18, 'HP', 'LaserJet Managed E55040', 1),
(19, 'HP', 'Laser 432fdn', 1),
(20, 'HP', 'Laser 408dn', 0),
(21, 'HP', 'LaserJet Managed MFP E57540', 1),
(22, 'RICOH', 'SP C352DN', 0),
(23, 'BROTHER', 'MFC-L6902DW', 1),
(24, 'BROTHER', 'MFC-L5912DW', 1),
(25, 'BROTHER', 'HL-L6202DW', 0),
(26, 'BROTHER', 'HL-L5212DW', 0),
(27, 'BROTHER', 'MFC-6702DW', 1),
(28, 'BROTHER', 'DCP-L5652DN', 1),
(29, 'BROTHER', 'HL-L6402DW', 0),
(30, 'BROTHER', 'MFC-J6955DW', 1),
(31, 'KYOCERA', 'TASKalfa 3551ci', 1),
(32, 'KYOCERA', 'ECOSYS MA4000cix', 1),
(33, 'KYOCERA', 'ECOSYS PA4000cx', 1),
(34, 'KYOCERA', 'ECOSYS P3155dn', 0),
(35, 'XEROX', 'VersaLink C400', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `modelos_conhecidos_backup`
--

CREATE TABLE `modelos_conhecidos_backup` (
  `id_modelo` int NOT NULL DEFAULT '0',
  `marca` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modelo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `eh_multifuncional` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `modelos_conhecidos_backup`
--

INSERT INTO `modelos_conhecidos_backup` (`id_modelo`, `marca`, `modelo`, `eh_multifuncional`) VALUES
(1, 'OKIDATA', 'ES4172LP MFP', 1),
(2, 'OKIDATA', 'ES5162LP MFP', 1),
(3, 'OKIDATA', 'ES5112 PN', 0),
(4, 'OKIDATA', 'ES6405N', 0),
(5, 'OKIDATA', 'MC780', 1),
(6, 'OKIDATA', 'MPS5502MB', 1),
(7, 'OKIDATA', 'C831N', 0),
(8, 'SAMSUNG', 'ProXpress M4070FR', 1),
(9, 'SAMSUNG', 'ProXpress SL-M4020ND', 0),
(10, 'SAMSUNG', 'SCX-5637', 1),
(11, 'SAMSUNG', 'ML-3710ND', 0),
(12, 'SAMSUNG', 'SL-C4062FX', 1),
(13, 'SAMSUNG', 'CLX-6260FR', 1),
(14, 'SAMSUNG', 'ProXpress M4580FX', 1),
(15, 'HP', 'LaserJet Managed E42540', 1),
(16, 'HP', 'LaserJet Managed E50145', 1),
(17, 'HP', 'LaserJet Managed E60165', 1),
(18, 'HP', 'LaserJet Managed E55040', 1),
(19, 'HP', 'Laser 432fdn', 1),
(20, 'HP', 'Laser 408dn', 0),
(21, 'HP', 'LaserJet Managed MFP E57540', 1),
(22, 'RICOH', 'SP C352DN', 0),
(23, 'BROTHER', 'MFC-L6902DW', 1),
(24, 'BROTHER', 'MFC-L5912DW', 1),
(25, 'BROTHER', 'HL-L6202DW', 0),
(26, 'BROTHER', 'HL-L5212DW', 0),
(27, 'BROTHER', 'MFC-6702DW', 1),
(28, 'BROTHER', 'DCP-L5652DN', 1),
(29, 'BROTHER', 'HL-L6402DW', 0),
(30, 'BROTHER', 'MFC-J6955DW', 1),
(31, 'KYOCERA', 'TASKalfa 3551ci', 1),
(32, 'KYOCERA', 'ECOSYS MA4000cix', 1),
(33, 'KYOCERA', 'ECOSYS PA4000cx', 1),
(34, 'KYOCERA', 'ECOSYS P3155dn', 0),
(35, 'XEROX', 'VersaLink C400', 0);

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
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `impressoras`
--
ALTER TABLE `impressoras`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_serie` (`numero_serie`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `fk_impressoras_modelos` (`id_modelo`),
  ADD KEY `fk_marca` (`marca`);

--
-- Índices para tabela `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id_marca`),
  ADD UNIQUE KEY `nome_marca` (`nome_marca`);

--
-- Índices para tabela `modelos_conhecidos`
--
ALTER TABLE `modelos_conhecidos`
  ADD PRIMARY KEY (`id_modelo`),
  ADD UNIQUE KEY `unica_marca_modelo` (`marca`,`modelo`),
  ADD KEY `idx_marca` (`marca`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT de tabela `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id_marca` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `modelos_conhecidos`
--
ALTER TABLE `modelos_conhecidos`
  MODIFY `id_modelo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de tabela `pecas_retiradas`
--
ALTER TABLE `pecas_retiradas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `impressoras`
--
ALTER TABLE `impressoras`
  ADD CONSTRAINT `fk_impressoras_modelos` FOREIGN KEY (`id_modelo`) REFERENCES `modelos_conhecidos` (`id_modelo`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_marca` FOREIGN KEY (`marca`) REFERENCES `marcas` (`nome_marca`);

--
-- Limitadores para a tabela `pecas_retiradas`
--
ALTER TABLE `pecas_retiradas`
  ADD CONSTRAINT `pecas_retiradas_ibfk_1` FOREIGN KEY (`impressora_id`) REFERENCES `impressoras` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
