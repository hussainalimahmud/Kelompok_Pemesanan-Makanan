-- ========================================================
-- Database: db_spk_mental (Arsitektur Kompleks Master-Detail)
-- Sistem Pendukung Keputusan Skrining Kesehatan Mental Mahasiswa
-- ========================================================

CREATE DATABASE IF NOT EXISTS `db_spk_mental` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_spk_mental`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `konseling_riwayat`;
DROP TABLE IF EXISTS `skrining_detail`;
DROP TABLE IF EXISTS `skrining_master`;
DROP TABLE IF EXISTS `skrining`;
DROP TABLE IF EXISTS `pertanyaan_indikator`;
DROP TABLE IF EXISTS `kategori_indikator`;
DROP TABLE IF EXISTS `mahasiswa`;
DROP TABLE IF EXISTS `dosen`;
DROP TABLE IF EXISTS `program_studi`;
SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
-- 1. Tabel Master: program_studi
-- --------------------------------------------------------
CREATE TABLE `program_studi` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_prodi` VARCHAR(10) NOT NULL UNIQUE,
  `nama_prodi` VARCHAR(100) NOT NULL,
  `jenjang` VARCHAR(10) NOT NULL DEFAULT 'S1',
  `fakultas` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `program_studi` (`id`, `kode_prodi`, `nama_prodi`, `jenjang`, `fakultas`) VALUES
(1, 'SI', 'Sistem Informasi', 'S1', 'Fakultas Ilmu Komputer'),
(2, 'TI', 'Teknik Informatika', 'S1', 'Fakultas Ilmu Komputer'),
(3, 'MI', 'Manajemen Informatika', 'D3', 'Fakultas Ilmu Komputer');

-- --------------------------------------------------------
-- 2. Tabel Master: dosen
-- --------------------------------------------------------
CREATE TABLE `dosen` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nidn` VARCHAR(30) NOT NULL UNIQUE,
  `nama_dosen` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `password_plain` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `prodi_id` INT NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`prodi_id`) REFERENCES `program_studi`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `dosen` (`id`, `nidn`, `nama_dosen`, `username`, `password`, `password_plain`, `email`, `prodi_id`) VALUES
(1, '0012058001', 'Dr. Hendra Kusuma, M.Kom.', 'dosen_hendra', '$2y$10$Xc/fFJ5ycomFvjW0HlAZXen40GVvCK1hm9RKmb6Oti/DCc.cpFvk.', 'Dosen#Hendra81', 'hendra.kusuma@univ.ac.id', 1),
(2, '0015088202', 'Dr. Sri Wahyuni, M.T.', 'dosen_sri', '$2y$10$AM.A9KDZ8lO4YTLkiBbjUu7vyQexiHlLSedsHuzQMtACNnuX7wxq.', 'Dosen@Wahyuni92', 'sri.wahyuni@univ.ac.id', 1),
(3, '0021038503', 'Agus Wibowo, S.Kom., M.Cs.', 'dosen_agus', '$2y$10$OGd.gDQZpc4iLBT1uhjfaO8CDvF8634voDiR6v1VstTsF8GH3ysQC', 'Dosen$Wibowo73', 'agus.wibowo@univ.ac.id', 1),
(4, '0008118704', 'Dewi Lestari, S.T., M.Kom.', 'dosen_dewi', '$2y$10$03g912.1T9NLm4aT3P0zyumesw8qn018Qii6648AwQbyY8fDhFjjK', 'Dosen%Lestari64', 'dewi.lestari@univ.ac.id', 1),
(5, '0030047905', 'Bambang Nugroho, M.Sc., Ph.D.', 'dosen_bambang', '$2y$10$GXkLVV5BnjL0kGJhG2iwIuFhDYgDNRIOFI64JxVWWGCyemPUgONeW', 'Dosen&Nugroho55', 'bambang.nugroho@univ.ac.id', 1);

-- --------------------------------------------------------
-- 3. Tabel Master: mahasiswa (100 Mahasiswa, 1 Dosen = 20 Mahasiswa)
-- --------------------------------------------------------
CREATE TABLE `mahasiswa` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nim` VARCHAR(20) NOT NULL UNIQUE,
  `nama_mahasiswa` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `password_plain` VARCHAR(100) NOT NULL,
  `prodi_id` INT NOT NULL DEFAULT 1,
  `angkatan` VARCHAR(10) NOT NULL DEFAULT '2023',
  `jenis_kelamin` ENUM('Laki-laki', 'Perempuan') NOT NULL,
  `usia` INT NOT NULL DEFAULT 20,
  `dosen_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`prodi_id`) REFERENCES `program_studi`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`dosen_id`) REFERENCES `dosen`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `mahasiswa` (`id`, `nim`, `nama_mahasiswa`, `password`, `password_plain`, `prodi_id`, `angkatan`, `jenis_kelamin`, `usia`, `dosen_id`) VALUES
(1, '230101001', 'Gilang Wibowo', '$2y$10$BPVWy0d6TC2oGTHsxahb6uQdRR3a1ZeIXxSTg6SGnsfKpDgj27Svm', 'Bintang@020', 1, '2023', 'Laki-laki', 20, 1),
(2, '230101002', 'Vivi Utomo', '$2y$10$xmaeT8D/fcCdzEP/U0OvFOPMVQxdlbs2KdC/8d2aqyBBWap8SWWdm', 'Melati#033', 1, '2023', 'Perempuan', 21, 1),
(3, '230101003', 'Bayu Damayanti', '$2y$10$PDg0PiOplL/tPrlAul7ZwetC/GiVY4mPwY6foIe4BvHJw3smLSndy', 'Samudra$046', 1, '2023', 'Laki-laki', 22, 1),
(4, '230101004', 'Hana Siregar', '$2y$10$gdr78rtEA/Zt8IvxKT/5AOJ79cjK/ehWC5LeCJUwV0FAKMSzH/i2O', 'Pelangi%059', 1, '2023', 'Perempuan', 19, 1),
(5, '230101005', 'Ahmad Gunawan', '$2y$10$ryhjsRbIrNqo6NGqeXTfie3MOTKayXV1QszpHj92Ub4ml0ZbG3jAi', 'Cahaya&072', 1, '2023', 'Laki-laki', 20, 1),
(6, '230101006', 'Nadya Ramadhan', '$2y$10$9ql0cpG5wsd.S/SQn1Bxd.8eQcHVRp8coRGPuyquUFQKOFcEYEe/y', 'Prestasi*085', 1, '2023', 'Perempuan', 21, 1),
(7, '230101007', 'Farhan Ginting', '$2y$10$xWshvrWvjJCmKzvb2C7IqOzqE1Lvq3rglwZL3KXxE0YSp3Q5nBzKy', 'Mentari!098', 1, '2023', 'Laki-laki', 22, 1),
(8, '230101008', 'Vivi Sari', '$2y$10$0y/b8bJgpOulbgPJcMMrzeu29pfglHy3LlkRlZa62SQMaAF6HXVny', 'Bahagia@111', 1, '2023', 'Perempuan', 19, 1),
(9, '230101009', 'Dimas Hermawan', '$2y$10$vpC45Oy5jxFOQ6jSW7oHAOmpz3SpqwTSof3LRZQmsooKu4KonYUdi', 'Bahagia#124', 1, '2023', 'Laki-laki', 20, 1),
(10, '230101010', 'Nurul Maharani', '$2y$10$d5Zdw8OD/LY8/shEscN7fOopVjjZouiCGEMYQO.fIwAPjxGg0WgzS', 'Ksatria$137', 1, '2023', 'Perempuan', 21, 1),
(11, '230101011', 'Kevin Subagyo', '$2y$10$aeQFkpBbwt/NmsQ24ahMae/PkAqeO0XE8yIsl31QjFYHgluoNoZ92', 'Mentari%150', 1, '2023', 'Laki-laki', 22, 1),
(12, '230101012', 'Kartika Siregar', '$2y$10$TkDJLyZvOn26XPjMTWTyWepiQNjvth4kCvVmXXHRLZjB4HZYncPtG', 'Gemilang&163', 1, '2023', 'Perempuan', 19, 1),
(13, '230101013', 'Xaverius Pratama', '$2y$10$Ehl9hAcVnWLuucd6cNjleOSKeR9XHTOjEUKb8n9WHET4IGcI2PsU2', 'Pesona*176', 1, '2023', 'Laki-laki', 20, 1),
(14, '230101014', 'Qonita Harahap', '$2y$10$8IYbBlNh1ffZgrcQRgNqtuKZNIz4zzMla0MSEzztQkTKePJTDONVu', 'Semangat!189', 1, '2023', 'Perempuan', 21, 1),
(15, '230101015', 'Farhan Pratama', '$2y$10$82RbkN7zA5sYgD5FTWFZaOQ8sgKAflF11DDTGX3NhNeSl17.TpEJ2', 'Cerdas@202', 1, '2023', 'Laki-laki', 22, 1),
(16, '230101016', 'Gita Utami', '$2y$10$isGxy/qIQwK.q9fzXMu1oepdnwz2nEOdTFmJY069hMSO3BbyWvVtG', 'Mandiri#215', 1, '2023', 'Perempuan', 19, 1),
(17, '230101017', 'Satria Hidayat', '$2y$10$3o9TfSydU.E9dzs1FZQKy.hiYdIFjiFT82UHxpkl8RqcR2tsi5T12', 'Gemilang$228', 1, '2023', 'Laki-laki', 20, 1),
(18, '230101018', 'Pratiwi Astuti', '$2y$10$3BT75z/2VHIlKcLW9ZonpOiZBRCr6I8pU17Cfb5Xg6X4vaX1MioUK', 'Merdeka%241', 1, '2023', 'Perempuan', 21, 1),
(19, '230101019', 'Syahrul Pasaribu', '$2y$10$jbCuRLlcpxZ8xzqEPBdvZejEFT8DgkZFwdlyUosc9knAa5ah7uL6y', 'Pelangi&254', 1, '2023', 'Laki-laki', 22, 1),
(20, '230101020', 'Rani Sari', '$2y$10$SuX8uTeq/BPAtAmVN52xzeBprdKNSJeznPihdtVHP3wFSRtc8iaqK', 'Lentera*267', 1, '2023', 'Perempuan', 19, 1),
(21, '230101021', 'Mochamad Susanti', '$2y$10$X5o11pzcP8ODZGzWmwCyzOy/d6O5EpHyAeaTOSbhJhpBKIhR385wi', 'Samudra!280', 1, '2023', 'Laki-laki', 20, 2),
(22, '230101022', 'Annisa Rahmawati', '$2y$10$kKAqKW8hvDWS1WNxSxP/SugBwIs.jFop/9hV86CrAEQKqYUXjZwJW', 'Bintang@293', 1, '2023', 'Perempuan', 21, 2),
(23, '230101023', 'Ahmad Syahputra', '$2y$10$.hZ0Fvjs.11RLmg9NAOy6uRJ0Okz3F0qbQHj2N97fmgfQmpRcAFmW', 'Impian#306', 1, '2023', 'Laki-laki', 22, 2),
(24, '230101024', 'Kirana Cahyani', '$2y$10$g6T/YTZAx7c9QRUr4QM9ReVuQZDhTvpyGtweZ0v2MIL7hNxhfTmd2', 'Pintar$319', 1, '2023', 'Perempuan', 19, 2),
(25, '230101025', 'Ahmad Handayani', '$2y$10$F5WQgNf2V7E1PQfS94i/UuYh20tKwt8WjtCI6OMOr3/uF7OA7zBTa', 'Harapan%332', 1, '2023', 'Laki-laki', 20, 2),
(26, '230101026', 'Fitri Astuti', '$2y$10$mWb.QbTWrja2CB9Fhazcm.g5HO43K8gOrX9y8zym8UrEufRksyng.', 'Melati&345', 1, '2023', 'Perempuan', 21, 2),
(27, '230101027', 'Gilang Saputra', '$2y$10$OlwR5w63zw1aO1lOJ835k.A35YSMIJliuxfOw6fQ470o7/IN0f0wK', 'Merdeka*358', 1, '2023', 'Laki-laki', 22, 2),
(28, '230101028', 'Kartika Ginting', '$2y$10$hvGgcCVMS4oPVfchy/BBWOQjLhmm.fMJskjP8LSLEGN3TxvC0kTti', 'Garuda!371', 1, '2023', 'Perempuan', 19, 2),
(29, '230101029', 'Erlangga Hermawan', '$2y$10$o1vzTwl5C/QG.vaNSA7UMektBgHYjqZ3msLjMn.3mO8whWBV1cUHK', 'Bintang@384', 1, '2023', 'Laki-laki', 20, 2),
(30, '230101030', 'Indah Kusumawati', '$2y$10$IBpZUphV9i5q0S3aZJ.PtuvZYYhWoSUYqsAQO/9oAI7CDKw7a1306', 'Harmoni#397', 1, '2023', 'Perempuan', 21, 2),
(31, '230101031', 'Mochamad Lestari', '$2y$10$XiaigDLmY3Jdja2HjVXSu.4I78hh6V8Y8wNzRGUaYwT6z11z.Pwlu', 'Bintang$410', 1, '2023', 'Laki-laki', 22, 2),
(32, '230101032', 'Indah Wardani', '$2y$10$d3lIzj85nuTaYcz6gsaSZOFZ.dIiQKOHwVuXJBF4L5GJHodDvKO3y', 'Lentera%423', 1, '2023', 'Perempuan', 19, 2),
(33, '230101033', 'Ahmad Pertiwi', '$2y$10$lhEwKGNbTFAiSUM9NCqcsubR7P28fnonG7zBN4htdodkFE3pziKB.', 'Angkasa&436', 1, '2023', 'Laki-laki', 20, 2),
(34, '230101034', 'Rani Hutapea', '$2y$10$blrgy0bugMKxGmVuxprxkeKz2CLXWU3u5mGL5AD26Dgl6Mv49CVqi', 'Wawasan*449', 1, '2023', 'Perempuan', 21, 2),
(35, '230101035', 'Budi Nasution', '$2y$10$qeNxi3xAdm8cgLMafViiTuIbqDjFHJKjw9.UTRIjlLq9BrbIYQOx6', 'Melati!462', 1, '2023', 'Laki-laki', 22, 2),
(36, '230101036', 'Shinta Nasution', '$2y$10$g57yFQiaQ3rkxJGxzlnRdu8IXF0LDZaxyqHZrodynE6jE/bOEczsa', 'Garuda@475', 1, '2023', 'Perempuan', 19, 2),
(37, '230101037', 'Mahesa Susanti', '$2y$10$oIfAgsar6Uc5kXURTnf8nuZhEPdLpMebnQfHx6vOLe4esKAQL/4ny', 'Laksana#488', 1, '2023', 'Laki-laki', 20, 2),
(38, '230101038', 'Risma Maulana', '$2y$10$sGZkzKEL..LafVeRN.22eOVsxLvjaEag5WnSvDYtJExqpDlq0eG/2', 'Pustaka$501', 1, '2023', 'Perempuan', 21, 2),
(39, '230101039', 'Wahyu Fitriani', '$2y$10$SqMg2N5xADdzEbWIGfCmkeRMj3bIeQNlD09Ti7kQ/kdMyOX63ZJDy', 'Garuda%514', 1, '2023', 'Laki-laki', 22, 2),
(40, '230101040', 'Intan Siregar', '$2y$10$voxPFpkDjVjXZaNPeHwWOOTjVqqyU3E25IWxivSLj8ddF5DaHNF7u', 'Wawasan&527', 1, '2023', 'Perempuan', 19, 2),
(41, '230101041', 'Rio Indriyani', '$2y$10$uNTQaERJEjr1fhkfwOz0P.BSqkV2/STz.iLVjpfK7kEYCVejhEx2e', 'Prestasi*540', 1, '2023', 'Laki-laki', 20, 3),
(42, '230101042', 'Pratiwi Wulandari', '$2y$10$pxjo/R9/J5pFJ6fk26Qrse9c2PixfFCg/SzykfT83SkuWNr5gpg8G', 'Impian!553', 1, '2023', 'Perempuan', 21, 3),
(43, '230101043', 'Oki Wibowo', '$2y$10$WZZZHRhKrMKMaLvCLjreceAvh/U77z/u/gY680P6Uy4xtmUv4n28e', 'Nusantara@566', 1, '2023', 'Laki-laki', 22, 3),
(44, '230101044', 'Cahya Utami', '$2y$10$QLP77je6GCODGU5dCp5fd.FI0Urxbv1WsjPaXIkx.mY.j4oGi2Nka', 'Bintang#579', 1, '2023', 'Perempuan', 19, 3),
(45, '230101045', 'Xaverius Pradana', '$2y$10$6d3VBeefuGnwCdaXk4tB4O4W3SfiiBi/MaTeu6xmEIFHB9ajUdhjy', 'Laksana$592', 1, '2023', 'Laki-laki', 20, 3),
(46, '230101046', 'Hana Pasaribu', '$2y$10$xvK4A3KEzmaecGUk850.vukdDewviwMdc87LpXlN8yV079aKrf5E2', 'Impian%605', 1, '2023', 'Perempuan', 21, 3),
(47, '230101047', 'Julian Kurniawan', '$2y$10$2FDCOdL5G4IIiHCZfLDTI.XaE6f.WAIU1/Vim9bYgN2bTLC.iprFa', 'Samudra&618', 1, '2023', 'Laki-laki', 22, 3),
(48, '230101048', 'Berlian Lubis', '$2y$10$2Bdx8GCdOWSLaQkR3wStYuIBWNEq5IIXgSnytgUSxzMD6ssMlbTC2', 'Harapan*631', 1, '2023', 'Perempuan', 19, 3),
(49, '230101049', 'Gilang Maulana', '$2y$10$ipSn1lWqkBQp/6FfLOyvoerzZ9mGNB9fM3YfmRDZTU7SW3WMUjTI.', 'Pustaka!644', 1, '2023', 'Laki-laki', 20, 3),
(50, '230101050', 'Qonita Wulandari', '$2y$10$1IJXP/wiRmUeS9SoxNnp4eHu67R0V6oKaiWuuWi.7smUqwk3UMc4O', 'Angkasa@657', 1, '2023', 'Perempuan', 21, 3),
(51, '230101051', 'Naufal Damayanti', '$2y$10$awE0YfhknQjSFSzEXi05iuuWg/Xf0aBZJ2DouCr9U4kBIACb1gBPy', 'Cahaya#670', 1, '2023', 'Laki-laki', 22, 3),
(52, '230101052', 'Jessica Maulana', '$2y$10$TW4uI6x/0M6/xjSNQPglOeJF5xQadSZWbwkry9nVClfCzbykmQSfe', 'Harmoni$683', 1, '2023', 'Perempuan', 19, 3),
(53, '230101053', 'Rizky Wardani', '$2y$10$L88RyuHbRONwoST.Z4gAHOF14nzwPzHZLK1ypvaxa9/f.p3JRcj22', 'Merdeka%696', 1, '2023', 'Laki-laki', 20, 3),
(54, '230101054', 'Nurul Puspita', '$2y$10$UA.lqPOJ3RC7klTGkQ/V2uM2M7X3Tdf5pkUlY6G9fKcRsik45GhRK', 'Merdeka&709', 1, '2023', 'Perempuan', 21, 3),
(55, '230101055', 'Fajar Permana', '$2y$10$p.h166hy8TckW83vE3UoFei5rxptKYPd91C2l.uNZ/UhDf4waSfqe', 'Lentera*722', 1, '2023', 'Laki-laki', 22, 3),
(56, '230101056', 'Yulia Nasution', '$2y$10$UfHxt7Hlet.UJmwrn6QNsez0dTnRNCaUw2sBQ.IJRq4hFkThlda9q', 'Sukses!735', 1, '2023', 'Perempuan', 19, 3),
(57, '230101057', 'Nur Siregar', '$2y$10$zhVHXt5nVGFbscS.bGuHGuozQ9N5y3ozL8uMRDXT.y5DgwF3SlPia', 'Pustaka@748', 1, '2023', 'Laki-laki', 20, 3),
(58, '230101058', 'Dina Harahap', '$2y$10$1jIAzstpti4I6kObsVh5gObpiQ9H6G3ipi9F.Kub39FW0ZInj0BTa', 'Gemilang#761', 1, '2023', 'Perempuan', 21, 3),
(59, '230101059', 'Kharisma Setiawan', '$2y$10$qTFO7njwqHmgFIPbQNWe7.eY.IBNuL2qFDDVWJs53/HhXOyPiSpqi', 'Nusantara$774', 1, '2023', 'Laki-laki', 22, 3),
(60, '230101060', 'Nia Santoso', '$2y$10$RPXnsmi85uuHiv878Acdg.Ju/uWGZ9//oEa.ZddztJ6yGOneIo7we', 'Lentera%787', 1, '2023', 'Perempuan', 19, 3),
(61, '230101061', 'Lucky Hermawan', '$2y$10$MdF7g4KFuHm5wdp/GK/chu84w4/0A5a9ZoqaOoVhtUtaeeVeKQHAS', 'Pintar&800', 1, '2023', 'Laki-laki', 20, 4),
(62, '230101062', 'Qonita Wijaya', '$2y$10$KPTeoW/Y2rF6etAXUOwbd.OAMIqpKDZ0w26VbO/i/7DkbsUF11RHW', 'Samudra*813', 1, '2023', 'Perempuan', 21, 4),
(63, '230101063', 'Fajar Harahap', '$2y$10$sbH.lmib1KEaWGZKjaAABORMovEEa827FxkAVbM.rSHIFE0A7ctnK', 'Garuda!826', 1, '2023', 'Laki-laki', 22, 4),
(64, '230101064', 'Wulan Susanti', '$2y$10$tfryfFOKH5cFLRm8njxSaO.rdwOc41X/dxmTeJiy6qygvGpWSIcoO', 'Pintar@839', 1, '2023', 'Perempuan', 19, 4),
(65, '230101065', 'Budi Ramadhan', '$2y$10$bG7YV5Yy04kqxQPynB3etegOsHdReEe1AGfGGAqXvjow8AuYVzf2e', 'Lentera#852', 1, '2023', 'Laki-laki', 20, 4),
(66, '230101066', 'Kartika Nasution', '$2y$10$/l7rfrzDSk60avVyFVjCc.YRdkg1LGt8bL3fsoIt99Ipc/Qfcov8C', 'Cahaya$865', 1, '2023', 'Perempuan', 21, 4),
(67, '230101067', 'Reza Maulana', '$2y$10$r2PwZdI5DANSCm5FWhxms.0OI4PGYHCR8n5Et44pRia8EoYIWWue.', 'Cerdas%878', 1, '2023', 'Laki-laki', 22, 4),
(68, '230101068', 'Maya Hermawan', '$2y$10$5Ks64.MQIaizoYknFUbzOewKREYmBOVi414lr.dZ9AeI9JxHKM7Le', 'Lentera&891', 1, '2023', 'Perempuan', 19, 4),
(69, '230101069', 'Yusuf Subagyo', '$2y$10$v.yccrTESQqcG8Uo7XVh.eVgQ.lzcw5sWIeSxSjrxM9NWw/9ynlJW', 'Pelangi*904', 1, '2023', 'Laki-laki', 20, 4),
(70, '230101070', 'Risma Anggraini', '$2y$10$jdfNET.h29p.wT1Hw3S2guNM/nZaizHPcOFRBlfYUmC/TzzrmBJle', 'Lentera!917', 1, '2023', 'Perempuan', 21, 4),
(71, '230101071', 'Bayu Santoso', '$2y$10$UM3cURbriKVybiJMofRPOepBtcqBCmTw8dHQz7bWJ7t5BN5utk1/u', 'Nusantara@930', 1, '2023', 'Laki-laki', 22, 4),
(72, '230101072', 'Kirana Simanjuntak', '$2y$10$m90J1vMiFqGDnX.TmS4sE.7Ne92xa7Pyt.aPsLcrYEIPZJyS5sFdi', 'Nusantara#943', 1, '2023', 'Perempuan', 19, 4),
(73, '230101073', 'Xaverius Harahap', '$2y$10$M1Ih8hZ/NOUWYsQBXgk55eRhyj2I.HbobckBxGiTYAV7B.jjZZHxa', 'Garuda$956', 1, '2023', 'Laki-laki', 20, 4),
(74, '230101074', 'Fauziah Cahyani', '$2y$10$mFi0LGqfPKnlUdTSaB2NhOxViRhFQvVwljXUeCDBMPImBVGaYRyIy', 'Gemilang%969', 1, '2023', 'Perempuan', 21, 4),
(75, '230101075', 'Wahyu Novitasari', '$2y$10$hGT.dJs3FJxmlDkTg.fCiOXRYivnA0S7oUZu32Yy/Ars3Y3kKzmw2', 'Angkasa&982', 1, '2023', 'Laki-laki', 22, 4),
(76, '230101076', 'Qonita Kusuma', '$2y$10$QA5/QKdPUNWN.TmRIw0vHOXpGU9.VLP14HOtE3HrdTD1YlvQRZWJ.', 'Angkasa*995', 1, '2023', 'Perempuan', 19, 4),
(77, '230101077', 'Kevin Ramadhan', '$2y$10$NBC06x8zm4lVKRX1B7xOiOkrlXdoW.oJULhnzxqtGDdJ/lmdRdOlS', 'Prestasi!009', 1, '2023', 'Laki-laki', 20, 4),
(78, '230101078', 'Nadya Simanjuntak', '$2y$10$2TAF6HBpLRNoDMXKv8OiAu2u/g/y/2s/haLtiLlmWNz90BY1vOjLa', 'Nusantara@022', 1, '2023', 'Perempuan', 21, 4),
(79, '230101079', 'Panji Hermawan', '$2y$10$XpryybBG8yaNdQrJFs87iO55lwxRmnjU4hrvJrsOmEa98.7m1sDx2', 'Ksatria#035', 1, '2023', 'Laki-laki', 22, 4),
(80, '230101080', 'Lestari Simanjuntak', '$2y$10$.uOyiv2M2vaRAcY/m7WtZePyD7rd5yGOhAzrsoOt1ZzUzl1mioiHu', 'Ksatria$048', 1, '2023', 'Perempuan', 19, 4),
(81, '230101081', 'Ahmad Lubis', '$2y$10$wxLkpWpdF8FrETKZyF2HceViLiLBgRfEbTjYiggtD53x88zvjsAzG', 'Lentera%061', 1, '2023', 'Laki-laki', 20, 5),
(82, '230101082', 'Cahya Handayani', '$2y$10$2ZMBfxT2dTveJEdm2YhvJ.r5OalM8.a6j161KRpbIPedB4BgJ0v0y', 'Bahagia&074', 1, '2023', 'Perempuan', 21, 5),
(83, '230101083', 'Julian Nugraha', '$2y$10$WpYGF5PjmaNFPIXyC1NCXeFL5RtcJ2qYCZ9okqYq0WYwpilQigQQi', 'Cahaya*087', 1, '2023', 'Laki-laki', 22, 5),
(84, '230101084', 'Zahra Pradana', '$2y$10$fYb86L1Y.YCwdRFtlIQKZuNRF7raoR1tinpjOcTOscBWTMVa3Pw5C', 'Bintang!100', 1, '2023', 'Perempuan', 19, 5),
(85, '230101085', 'Xaverius Kusuma', '$2y$10$osmRLV27/culkn8TjEsHn.YBatZJOuLUQZ4U9lhZDATiFPdT8yTTK', 'Wawasan@113', 1, '2023', 'Laki-laki', 20, 5),
(86, '230101086', 'Jihan Ginting', '$2y$10$Ogkzee73u4cFdLS0I2m6lOhJTPOenkS8UMCnN55n/w139zHQgM5J2', 'Permata#126', 1, '2023', 'Perempuan', 21, 5),
(87, '230101087', 'Vino Novitasari', '$2y$10$dTm/80LrL9lLBW7MEDsAQ.GQMkkzShUtRJS11FiK3tKRBRQXrZEPS', 'Bahagia$139', 1, '2023', 'Laki-laki', 22, 5),
(88, '230101088', 'Gisela Indriyani', '$2y$10$6iDDj30WDPlyyUERUOTMXOJ5jF.xfuW1JN17EhxtOyzWY6RkdDQ7u', 'Prestasi%152', 1, '2023', 'Perempuan', 19, 5),
(89, '230101089', 'Xaverius Maulana', '$2y$10$uhv0iPepvAJkW6oLJWGE4OphKZosUAD31iz07hMAgJNVL8vzLO4Vm', 'Ksatria&165', 1, '2023', 'Laki-laki', 20, 5),
(90, '230101090', 'Lestari Ginting', '$2y$10$BlO/85OLLg876JuY/xj8WuAvImKNR4Goj8SNcG6SqzfIqul17AB.y', 'Pesona*178', 1, '2023', 'Perempuan', 21, 5),
(91, '230101091', 'Pandu Lubis', '$2y$10$3n.C1NOOn/GMdK6lzZ6xu.YAkofUoox/OseVUnRIKgLBtrIBqJ2ZS', 'Prestasi!191', 1, '2023', 'Laki-laki', 22, 5),
(92, '230101092', 'Cahya Wardani', '$2y$10$g34bJycX.HlFhHmiLUbtsu2IVuxG3hcD1U89v7l1T82XxkPSLFTD2', 'Samudra@204', 1, '2023', 'Perempuan', 19, 5),
(93, '230101093', 'Vino Sari', '$2y$10$/hyBT.qoDJR5DmiSsrwzCuJpPXB8OZgJqF10roqoWKYwCgpVszZha', 'Bahagia#217', 1, '2023', 'Laki-laki', 20, 5),
(94, '230101094', 'Endah Lestari', '$2y$10$Ga.yKuRI5szhHhPKG3FiLuQoMR/X4b0aGKtRewgYPqSt7y6FP0ScO', 'Pustaka$230', 1, '2023', 'Perempuan', 21, 5),
(95, '230101095', 'Raditya Lubis', '$2y$10$3457tzgrWES0e/EhzGzFAOut/OUq3BQP0S2cM6B.6qUOAF2iP2B7i', 'Prestasi%243', 1, '2023', 'Laki-laki', 22, 5),
(96, '230101096', 'Ulfa Lestari', '$2y$10$7Zl04yjxrfZlnJPKAGs59O/KxjHG4dQvCCj/VJ9DJXYHKlH9vGbVi', 'Harmoni&256', 1, '2023', 'Perempuan', 19, 5),
(97, '230101097', 'Oki Siregar', '$2y$10$lFdMbrxTVeaSiMbvOv6cKenH3Qsv5I.pZ.f4TtA9.qTyqp2Xd/jsO', 'Angkasa*269', 1, '2023', 'Laki-laki', 20, 5),
(98, '230101098', 'Alya Safitri', '$2y$10$W8bDxZqMjHH5O72av6sGIOfTj1VQbFq0NWn2BLfS6lh65SDEJSWJu', 'Prestasi!282', 1, '2023', 'Perempuan', 21, 5),
(99, '230101099', 'Rudi Wijaya', '$2y$10$SB.qu/k830x.P4T.eMMuZe5xEg/cTJ4fA0ct7do8orkFz2vPKJyma', 'Ksatria@295', 1, '2023', 'Laki-laki', 22, 5),
(100, '230101100', 'Jessica Fitriani', '$2y$10$aGA0hTSV5Mcny7/nINd.neF88ugfMPVPLuoF/3In4s2fH/cJFOHSm', 'Harapan#308', 1, '2023', 'Perempuan', 19, 5);

-- --------------------------------------------------------
-- 4. Tabel Master: kategori_indikator
-- --------------------------------------------------------
CREATE TABLE `kategori_indikator` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_kategori` VARCHAR(20) NOT NULL UNIQUE,
  `nama_kategori` VARCHAR(100) NOT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `urutan` INT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `kategori_indikator` (`id`, `kode_kategori`, `nama_kategori`, `deskripsi`, `urutan`) VALUES
(1, 'AKADEMIK', 'Kondisi Akademik & Pembelajaran', 'Indikator performa studi, durasi belajar, dan tingkat tekanan kurikulum.', 1),
(2, 'FINANSIAL', 'Kondisi Finansial & Pekerjaan', 'Tingkat tekanan keuangan kuliah dan beban kerja sampingan mahasiswa.', 2),
(3, 'PSIKOLOGIS', 'Gaya Hidup & Kesejahteraan Mental', 'Pola tidur harian, kualitas nutrisi makan, riwayat depresi, dan pemikiran negatif.', 3);

-- --------------------------------------------------------
-- 5. Tabel Master: pertanyaan_indikator (12 Parameter Fitur ML)
-- --------------------------------------------------------
CREATE TABLE `pertanyaan_indikator` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kategori_id` INT NOT NULL,
  `kode_fitur` VARCHAR(50) NOT NULL UNIQUE,
  `teks_pertanyaan` TEXT NOT NULL,
  `tipe_input` ENUM('skala_1_5', 'skala_0_5', 'pilihan', 'angka', 'desimal') NOT NULL,
  `opsi_pilihan` JSON DEFAULT NULL,
  `keterangan_bantuan` TEXT DEFAULT NULL,
  `bobot_relatif` DECIMAL(5,2) NOT NULL DEFAULT 1.00,
  `urutan` INT NOT NULL DEFAULT 1,
  FOREIGN KEY (`kategori_id`) REFERENCES `kategori_indikator`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pertanyaan_indikator` (`id`, `kategori_id`, `kode_fitur`, `teks_pertanyaan`, `tipe_input`, `opsi_pilihan`, `keterangan_bantuan`, `bobot_relatif`, `urutan`) VALUES
(1, 1, 'jenis_kelamin', 'Jenis Kelamin Mahasiswa', 'pilihan', '[\"Laki-laki\", \"Perempuan\"]', 'Karakteristik demografis biologis mahasiswa.', 1, 1),
(2, 1, 'usia', 'Usia Mahasiswa (Tahun)', 'angka', NULL, 'Usia kronologis dalam tahun.', 1, 2),
(3, 1, 'ipk', 'Indeks Prestasi Kumulatif (IPK Terakhir)', 'desimal', NULL, 'Nilai IPK skala 0.00 - 4.00 (dikonversi ke skala model).', 2, 3),
(4, 1, 'tekanan_akademik', 'Tingkat Tekanan Akademik yang Dirasakan', 'skala_1_5', '{\"1\":\"Sangat Ringan\",\"2\":\"Ringan\",\"3\":\"Sedang\",\"4\":\"Berat\",\"5\":\"Sangat Berat\"}', 'Beban tugas, ujian, dan tenggat waktu perkuliahan.', 3, 4),
(5, 1, 'kepuasan_belajar', 'Kepuasan Terhadap Proses & Lingkungan Belajar', 'skala_1_5', '{\"1\":\"Sangat Tidak Puas\",\"2\":\"Tidak Puas\",\"3\":\"Cukup Puas\",\"4\":\"Puas\",\"5\":\"Sangat Puas\"}', 'Evaluasi kenyamanan kurikulum dan metode pengajaran dosen.', 2, 5),
(6, 1, 'jam_belajar_harian', 'Rata-rata Waktu Belajar Mandiri per Hari (Jam)', 'angka', NULL, 'Waktu yang dihabiskan untuk belajar mandiri di luar jam kelas.', 1.5, 6),
(7, 2, 'tekanan_keuangan', 'Beban / Tekanan Finansial & Biaya Hidup Kuliah', 'skala_1_5', '{\"1\":\"Tidak Ada Tekanan\",\"2\":\"Ringan\",\"3\":\"Sedang\",\"4\":\"Berat\",\"5\":\"Sangat Kritis\"}', 'Tekanan terkait UKT, biaya hidup harian, dan tempat tinggal.', 2.5, 7),
(8, 2, 'tekanan_pekerjaan', 'Tekanan Pekerjaan Sampingan / Freelance / Magang', 'skala_0_5', '{\"0\":\"Tidak Bekerja\",\"1\":\"Sangat Ringan\",\"2\":\"Ringan\",\"3\":\"Sedang\",\"4\":\"Berat\",\"5\":\"Sangat Melelahkan\"}', 'Beban ganda bagi mahasiswa yang bekerja paruh waktu.', 2, 8),
(9, 3, 'durasi_tidur', 'Rata-rata Durasi Tidur Harian', 'pilihan', '[\"Kurang dari 5 jam\", \"5-6 jam\", \"7-8 jam\", \"Lebih dari 8 jam\"]', 'Pola istirahat tidur pada malam hari.', 2.5, 9),
(10, 3, 'pola_makan', 'Kualitas & Keteraturan Pola Makan', 'pilihan', '[\"Sehat\", \"Cukup/Sedang\", \"Tidak Sehat\"]', 'Keteraturan jam makan dan gizi seimbang harian.', 2, 10),
(11, 3, 'riwayat_keluarga', 'Riwayat Gangguan Mental dalam Keluarga', 'pilihan', '[\"Tidak\", \"Ya\"]', 'Faktor genetik atau predisposisi riwayat psikiatri keluarga.', 2, 11),
(12, 3, 'pikiran_menyakiti_diri', 'Pernahkah Muncul Pikiran untuk Menyakiti Diri / Putus Asa?', 'pilihan', '[\"Tidak\", \"Ya\"]', 'Indikator krisis psikologis dan kecenderungan impulsif.', 4, 12);

-- --------------------------------------------------------
-- 6. Tabel Master Transaksi: skrining_master
-- --------------------------------------------------------
CREATE TABLE `skrining_master` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_sesi` VARCHAR(30) NOT NULL UNIQUE,
  `mahasiswa_id` INT NOT NULL,
  `dosen_id` INT NOT NULL,
  `tahun_akademik` VARCHAR(20) NOT NULL DEFAULT '2025/2026 Ganjil',
  `ipk_snapshot` DECIMAL(4,2) NOT NULL,
  `usia_snapshot` INT NOT NULL,
  `jenis_kelamin_snapshot` VARCHAR(20) NOT NULL,
  `hasil_prediksi` TINYINT(1) NOT NULL COMMENT '0: Rendah, 1: Tinggi',
  `kategori` VARCHAR(50) NOT NULL,
  `probabilitas_rendah` DECIMAL(5,2) NOT NULL,
  `probabilitas_tinggi` DECIMAL(5,2) NOT NULL,
  `catatan_refleksi` TEXT NOT NULL,
  `faktor_utama` TEXT NOT NULL,
  `status_konseling` ENUM('Belum Ditindaklanjuti', 'Dijadwalkan Konseling', 'Selesai Konseling') NOT NULL DEFAULT 'Belum Ditindaklanjuti',
  `tanggal_skrining` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`dosen_id`) REFERENCES `dosen`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 7. Tabel Detail Transaksi: skrining_detail (Implementasi Master-Detail)
-- --------------------------------------------------------
CREATE TABLE `skrining_detail` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `skrining_id` INT NOT NULL,
  `pertanyaan_id` INT NOT NULL,
  `kode_fitur` VARCHAR(50) NOT NULL,
  `nilai_raw` VARCHAR(100) NOT NULL,
  `nilai_numerik` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `interpretasi_singkat` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`skrining_id`) REFERENCES `skrining_master`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`pertanyaan_id`) REFERENCES `pertanyaan_indikator`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 8. Tabel Detail Transaksi: konseling_riwayat (Riwayat Konsultasi Dosen)
-- --------------------------------------------------------
CREATE TABLE `konseling_riwayat` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `skrining_id` INT NOT NULL,
  `dosen_id` INT NOT NULL,
  `mahasiswa_id` INT NOT NULL,
  `tanggal_konseling` VARCHAR(50) DEFAULT NULL,
  `tempat_metode` VARCHAR(100) DEFAULT 'Ruang Dosen PA / Tatap Muka',
  `catatan_bimbingan` TEXT NOT NULL,
  `solusi_tindak_lanjut` TEXT DEFAULT NULL,
  `status_pertemuan` ENUM('Dijadwalkan', 'Selesai', 'Dibatalkan') NOT NULL DEFAULT 'Dijadwalkan',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`skrining_id`) REFERENCES `skrining_master`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`dosen_id`) REFERENCES `dosen`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `skrining_master` (`id`, `kode_sesi`, `mahasiswa_id`, `dosen_id`, `tahun_akademik`, `ipk_snapshot`, `usia_snapshot`, `jenis_kelamin_snapshot`, `hasil_prediksi`, `kategori`, `probabilitas_rendah`, `probabilitas_tinggi`, `catatan_refleksi`, `faktor_utama`, `status_konseling`) VALUES
(1, 'SKR-2026-0001', 1, 1, '2025/2026 Ganjil', 3.75, 20, 'Laki-laki', 0, 'Tingkat Stress/Risiko Rendah', 93.5, 6.5, 'Kondisi psikologis dan pola hidupmu relatif stabil dan sehat. Pertahankan manajemen waktu, pola istirahat, dan lingkungan belajar yang positif!', 'Tekanan Akademik (24.71%), Tekanan Keuangan (12.01%), Jam Belajar Harian (8.45%)', 'Belum Ditindaklanjuti'),
(2, 'SKR-2026-0002', 2, 1, '2025/2026 Ganjil', 3.05, 21, 'Perempuan', 1, 'Tingkat Stress/Risiko Tinggi', 17.8, 82.2, 'Hasil indikasi menunjukkan kamu sedang mengalami beban pikiran atau tekanan psikologis yang cukup berat. Disarankan untuk beristirahat sejenak dan berkonsultasi dengan Dosen PA atau konselor kampus.', 'Pikiran Menyakiti Diri (35.61%), Tekanan Akademik (24.71%), Tekanan Keuangan (12.01%)', 'Dijadwalkan Konseling'),
(3, 'SKR-2026-0003', 21, 2, '2025/2026 Ganjil', 3.2, 20, 'Laki-laki', 1, 'Tingkat Stress/Risiko Tinggi', 32.1, 67.9, 'Hasil indikasi menunjukkan kamu sedang mengalami beban pikiran atau tekanan psikologis yang cukup berat. Disarankan untuk beristirahat sejenak dan berkonsultasi dengan Dosen PA atau konselor kampus.', 'Tekanan Akademik (24.71%), Tekanan Keuangan (12.01%), Durasi Tidur (10.15%)', 'Belum Ditindaklanjuti');

INSERT INTO `skrining_detail` (`skrining_id`, `pertanyaan_id`, `kode_fitur`, `nilai_raw`, `nilai_numerik`, `interpretasi_singkat`) VALUES
(1, 1, 'jenis_kelamin', 'Laki-laki', 0, 'Normal'),
(1, 2, 'usia', '20', 20, 'Normal'),
(1, 3, 'ipk', '3.75', 3.75, 'Sangat Baik'),
(1, 4, 'tekanan_akademik', '2', 2, 'Ringan'),
(1, 5, 'kepuasan_belajar', '4', 4, 'Puas'),
(1, 6, 'jam_belajar_harian', '4', 4, 'Ideal'),
(1, 7, 'tekanan_keuangan', '2', 2, 'Ringan'),
(1, 8, 'tekanan_pekerjaan', '0', 0, 'Tidak Bekerja'),
(1, 9, 'durasi_tidur', '7-8 jam', 8, 'Cukup & Ideal'),
(1, 10, 'pola_makan', 'Sehat', 3, 'Gizi Seimbang'),
(1, 11, 'riwayat_keluarga', 'Tidak', 0, 'Tidak Ada'),
(1, 12, 'pikiran_menyakiti_diri', 'Tidak', 0, 'Aman'),
(2, 1, 'jenis_kelamin', 'Perempuan', 1, 'Normal'),
(2, 2, 'usia', '21', 21, 'Normal'),
(2, 3, 'ipk', '3.05', 3.05, 'Cukup'),
(2, 4, 'tekanan_akademik', '5', 5, 'Sangat Berat'),
(2, 5, 'kepuasan_belajar', '2', 2, 'Kurang Puas'),
(2, 6, 'jam_belajar_harian', '7', 7, 'Intensitas Tinggi'),
(2, 7, 'tekanan_keuangan', '4', 4, 'Beban Berat'),
(2, 8, 'tekanan_pekerjaan', '2', 2, 'Ringan'),
(2, 9, 'durasi_tidur', 'Kurang dari 5 jam', 4, 'Kurang / Begadang'),
(2, 10, 'pola_makan', 'Tidak Sehat', 1, 'Sering Terlewat'),
(2, 11, 'riwayat_keluarga', 'Ya', 1, 'Ada Riwayat'),
(2, 12, 'pikiran_menyakiti_diri', 'Ya', 1, 'Sangat Butuh Perhatian'),
(3, 1, 'jenis_kelamin', 'Laki-laki', 0, 'Normal'),
(3, 2, 'usia', '20', 20, 'Normal'),
(3, 3, 'ipk', '3.20', 3.2, 'Baik'),
(3, 4, 'tekanan_akademik', '4', 4, 'Berat'),
(3, 5, 'kepuasan_belajar', '2', 2, 'Kurang Puas'),
(3, 6, 'jam_belajar_harian', '5', 5, 'Wajar'),
(3, 7, 'tekanan_keuangan', '3', 3, 'Sedang'),
(3, 8, 'tekanan_pekerjaan', '1', 1, 'Ringan'),
(3, 9, 'durasi_tidur', '5-6 jam', 6, 'Kurang'),
(3, 10, 'pola_makan', 'Cukup/Sedang', 2, 'Cukup'),
(3, 11, 'riwayat_keluarga', 'Tidak', 0, 'Tidak Ada'),
(3, 12, 'pikiran_menyakiti_diri', 'Tidak', 0, 'Aman');

INSERT INTO `konseling_riwayat` (`skrining_id`, `dosen_id`, `mahasiswa_id`, `tanggal_konseling`, `tempat_metode`, `catatan_bimbingan`, `solusi_tindak_lanjut`, `status_pertemuan`) VALUES
(2, 1, 2, 'Senin, 17 November 2026 pukul 10.00 WIB', 'Ruang Dosen PA Gedung B Lantai 2', 'Diharapkan hadir konseling tatap muka untuk evaluasi beban SKS semester ini dan manajemen tekanan belajar.', 'Disarankan mengambil beban maksimal 18 SKS di semester depan serta mengikuti workshop mindfulness konseling kampus.', 'Dijadwalkan');
