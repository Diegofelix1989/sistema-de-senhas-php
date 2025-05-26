-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 24/05/2025 às 21:40
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `gerenciador_filas`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `filas`
--

CREATE TABLE `filas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `tipo` enum('comum','prioritaria') DEFAULT NULL,
  `local_id` int(11) DEFAULT NULL,
  `prefixo` varchar(5) NOT NULL,
  `tamanho_senha` int(11) DEFAULT 3
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `filas`
--

INSERT INTO `filas` (`id`, `nome`, `tipo`, `local_id`, `prefixo`, `tamanho_senha`) VALUES
(1, 'NORMAL', 'comum', 1, 'N', 3),
(2, 'PRIORIDADE', 'prioritaria', 1, 'PR', 3),
(4, 'fila0002', 'comum', 1, 'F2', 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `guiches`
--

CREATE TABLE `guiches` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `local_id` int(11) NOT NULL,
  `fila_id` int(11) NOT NULL,
  `status_uso` enum('disponivel','em_uso') NOT NULL DEFAULT 'disponivel',
  `status_ativo` enum('ativo','desativado') NOT NULL DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `guiches`
--

INSERT INTO `guiches` (`id`, `nome`, `local_id`, `fila_id`, `status_uso`, `status_ativo`) VALUES
(1, 'GUICHE 01', 1, 1, 'disponivel', 'ativo'),
(2, 'GUICHE 02', 1, 2, 'disponivel', 'ativo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `locais`
--

CREATE TABLE `locais` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `locais`
--

INSERT INTO `locais` (`id`, `nome`, `descricao`, `criado_em`) VALUES
(1, 'UnaspStore2', '', '2025-05-13 00:10:39'),
(3, 'SOE-ENSINO-MEDIO', '', '2025-05-18 19:48:16');

-- --------------------------------------------------------

--
-- Estrutura para tabela `publicidades`
--

CREATE TABLE `publicidades` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `tipo_midia` enum('imagem','video','texto') NOT NULL,
  `media_path` varchar(255) DEFAULT NULL,
  `duracao` int(11) NOT NULL,
  `id_tela` int(11) NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `publicidades`
--

INSERT INTO `publicidades` (`id`, `titulo`, `tipo_midia`, `media_path`, `duracao`, `id_tela`, `data_criacao`) VALUES
(21, 'TESTE01', 'video', '../uploads/683204db59c89.mp4', 10, 1, '2025-05-24 17:41:47'),
(22, 'jjnjnjnj', 'imagem', '../uploads/68320e208e545_qrcode_claude.ai.png', 5, 1, '2025-05-24 18:21:20');

-- --------------------------------------------------------

--
-- Estrutura para tabela `senhas`
--

CREATE TABLE `senhas` (
  `id` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `fila_id` int(11) DEFAULT NULL,
  `status` enum('aguardando','em_atendimento','atendida') DEFAULT 'aguardando',
  `chamada_por` int(11) DEFAULT NULL,
  `guiche_id` int(11) DEFAULT NULL,
  `chamada_em` timestamp NULL DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atendimento_iniciado_em` timestamp NULL DEFAULT NULL,
  `atendimento_finalizado_em` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `senhas`
--

INSERT INTO `senhas` (`id`, `numero`, `fila_id`, `status`, `chamada_por`, `guiche_id`, `chamada_em`, `criado_em`, `atendimento_iniciado_em`, `atendimento_finalizado_em`) VALUES
(20, 1, 4, 'atendida', 1, 1, '2025-05-23 04:26:35', '2025-05-18 23:05:38', '2025-05-23 04:26:56', '2025-05-23 04:27:31'),
(21, 1, 2, 'em_atendimento', 1, 2, '2025-05-19 04:06:57', '2025-05-18 23:05:48', NULL, NULL),
(22, 1, 1, 'atendida', 1, 2, '2025-05-19 04:09:10', '2025-05-18 23:05:59', '2025-05-19 04:12:13', '2025-05-19 04:12:16'),
(23, 2, 1, 'atendida', 1, 2, '2025-05-23 04:45:28', '2025-05-22 23:40:38', '2025-05-23 04:46:10', '2025-05-23 04:46:43'),
(24, 2, 2, 'atendida', 1, 1, '2025-05-23 04:45:39', '2025-05-22 23:40:55', '2025-05-23 04:45:53', '2025-05-23 04:46:50'),
(25, 3, 2, 'atendida', 1, 2, '2025-05-23 05:26:05', '2025-05-23 00:15:33', '2025-05-23 05:27:13', '2025-05-23 05:27:44'),
(26, 2, 4, 'atendida', 1, 1, '2025-05-23 05:26:49', '2025-05-23 00:25:09', '2025-05-23 05:27:01', '2025-05-23 05:27:50'),
(27, 3, 4, 'atendida', 1, 2, '2025-05-23 06:09:04', '2025-05-23 01:06:40', '2025-05-23 06:08:31', '2025-05-23 06:11:07'),
(28, 3, 1, 'atendida', 1, 2, '2025-05-23 06:23:37', '2025-05-23 01:07:33', '2025-05-23 06:23:01', '2025-05-23 06:23:54');

-- --------------------------------------------------------

--
-- Estrutura para tabela `telas`
--

CREATE TABLE `telas` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `local_id` int(11) NOT NULL,
  `fila_id` int(11) NOT NULL,
  `tipo_exibicao` enum('senhas','publicidade','ambos') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `telas`
--

INSERT INTO `telas` (`id`, `nome`, `local_id`, `fila_id`, `tipo_exibicao`) VALUES
(1, 'TELA012', 1, 1, 'ambos');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `tipo` enum('admin','atendente') NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `tipo`, `criado_em`) VALUES
(1, 'diego', 'diegofelixbruna1989@gmail.com', '$2y$10$CqImrEdhSsedWUViDvvg1eZ2ssOtT32dY5ggPQ3Ehq6.ujPEMnscG', 'admin', '2025-05-11 01:43:10');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `filas`
--
ALTER TABLE `filas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uc_prefixo_local` (`prefixo`,`local_id`),
  ADD KEY `local_id` (`local_id`);

--
-- Índices de tabela `guiches`
--
ALTER TABLE `guiches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `local_id` (`local_id`),
  ADD KEY `fila_id` (`fila_id`);

--
-- Índices de tabela `locais`
--
ALTER TABLE `locais`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `publicidades`
--
ALTER TABLE `publicidades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tela` (`id_tela`);

--
-- Índices de tabela `senhas`
--
ALTER TABLE `senhas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fila_id` (`fila_id`),
  ADD KEY `chamada_por` (`chamada_por`),
  ADD KEY `fk_guiche_id` (`guiche_id`);

--
-- Índices de tabela `telas`
--
ALTER TABLE `telas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `local_id` (`local_id`),
  ADD KEY `fila_id` (`fila_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `filas`
--
ALTER TABLE `filas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `guiches`
--
ALTER TABLE `guiches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `locais`
--
ALTER TABLE `locais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `publicidades`
--
ALTER TABLE `publicidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `senhas`
--
ALTER TABLE `senhas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de tabela `telas`
--
ALTER TABLE `telas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1991;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `filas`
--
ALTER TABLE `filas`
  ADD CONSTRAINT `filas_ibfk_1` FOREIGN KEY (`local_id`) REFERENCES `locais` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `guiches`
--
ALTER TABLE `guiches`
  ADD CONSTRAINT `guiches_ibfk_1` FOREIGN KEY (`local_id`) REFERENCES `locais` (`id`),
  ADD CONSTRAINT `guiches_ibfk_2` FOREIGN KEY (`fila_id`) REFERENCES `filas` (`id`);

--
-- Restrições para tabelas `publicidades`
--
ALTER TABLE `publicidades`
  ADD CONSTRAINT `publicidades_ibfk_1` FOREIGN KEY (`id_tela`) REFERENCES `telas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `senhas`
--
ALTER TABLE `senhas`
  ADD CONSTRAINT `fk_guiche_id` FOREIGN KEY (`guiche_id`) REFERENCES `guiches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `senhas_ibfk_1` FOREIGN KEY (`fila_id`) REFERENCES `filas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `senhas_ibfk_2` FOREIGN KEY (`chamada_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `telas`
--
ALTER TABLE `telas`
  ADD CONSTRAINT `telas_ibfk_1` FOREIGN KEY (`local_id`) REFERENCES `locais` (`id`),
  ADD CONSTRAINT `telas_ibfk_2` FOREIGN KEY (`fila_id`) REFERENCES `filas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
