-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: localhost:3306
-- Létrehozás ideje: 2024. Jún 04. 21:26
-- Kiszolgáló verziója: 10.6.17-MariaDB-cll-lve-log
-- PHP verzió: 8.1.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `yltyuznq_szempo`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `areatypes`
--

CREATE TABLE `areatypes` (
  `id` int(10) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- A tábla adatainak kiíratása `area_types`
--

INSERT INTO `areatypes` (`id`, `name`) VALUES
(1, 'akna'),
(2, 'akna-alsó'),
(3, 'akna-felső'),
(4, 'alagút'),
(5, 'alsórakpart'),
(6, 'arborétum'),
(7, 'autóút'),
(8, 'barakképület'),
(9, 'barlang'),
(10, 'bejáró'),
(11, 'bekötőút'),
(12, 'bánya'),
(13, 'bányatelep'),
(14, 'bástya'),
(15, 'bástyája'),
(16, 'csárda'),
(17, 'csónakházak'),
(18, 'domb'),
(19, 'dűlő'),
(20, 'dűlők'),
(21, 'dűlősor'),
(22, 'dűlőterület'),
(23, 'dűlőút'),
(24, 'egyetemváros'),
(25, 'egyéb'),
(26, 'elágazás'),
(27, 'emlékút'),
(28, 'erdészház'),
(29, 'erdészlak'),
(30, 'erdő'),
(31, 'erdősor'),
(32, 'fasor'),
(33, 'fasora'),
(34, 'felső'),
(35, 'forduló'),
(36, 'főmérnökség'),
(37, 'főtér'),
(38, 'főút'),
(39, 'föld'),
(40, 'gyár'),
(41, 'gyártelep'),
(42, 'gyárváros'),
(43, 'gyümölcsös'),
(44, 'gát'),
(45, 'gátsor'),
(46, 'gátőrház'),
(47, 'határsor'),
(48, 'határút'),
(49, 'hegy'),
(50, 'hegyhát'),
(51, 'hegyhát dűlő'),
(52, 'hegyhát'),
(53, 'köz'),
(54, 'hrsz'),
(55, 'hrsz.'),
(56, 'ház'),
(57, 'hídfő'),
(58, 'iskola'),
(59, 'játszótér'),
(60, 'kapu'),
(61, 'kastély'),
(62, 'kert'),
(63, 'kertsor'),
(64, 'kerület'),
(65, 'kilátó'),
(66, 'kioszk'),
(67, 'kocsiszín'),
(68, 'kolónia'),
(69, 'korzó'),
(70, 'kultúrpark'),
(71, 'kunyhó'),
(72, 'kör'),
(73, 'körtér'),
(74, 'körvasútsor'),
(75, 'körzet'),
(76, 'körönd'),
(77, 'körút'),
(78, 'köz'),
(79, 'kút'),
(80, 'kültelek'),
(81, 'lakóház'),
(82, 'lakókert'),
(83, 'lakónegyed'),
(84, 'lakópark'),
(85, 'lakótelep'),
(86, 'lejtő'),
(87, 'lejáró'),
(88, 'liget'),
(89, 'lépcső'),
(90, 'major'),
(91, 'malom'),
(92, 'menedékház'),
(93, 'munkásszálló'),
(94, 'mélyút'),
(95, 'műút'),
(96, 'oldal'),
(97, 'orom'),
(98, 'park'),
(99, 'parkja'),
(100, 'parkoló'),
(101, 'part'),
(102, 'pavilon'),
(103, 'piac'),
(104, 'pihenő'),
(105, 'pince'),
(106, 'pincesor'),
(107, 'postafiók'),
(108, 'puszta'),
(109, 'pálya'),
(110, 'pályaudvar'),
(111, 'rakpart'),
(112, 'repülőtér'),
(113, 'rész'),
(114, 'rét'),
(115, 'sarok'),
(116, 'sor'),
(117, 'sora'),
(118, 'sportpálya'),
(119, 'sporttelep'),
(120, 'stadion'),
(121, 'strandfürdő'),
(122, 'sugárút'),
(123, 'szer'),
(124, 'sziget'),
(125, 'szivattyútelep'),
(126, 'szállás'),
(127, 'szállások'),
(128, 'szél'),
(129, 'szőlő'),
(130, 'szőlőhegy'),
(131, 'szőlők'),
(132, 'sánc'),
(133, 'sávház'),
(134, 'sétány'),
(135, 'tag'),
(136, 'tanya'),
(137, 'tanyák'),
(138, 'telep'),
(139, 'temető'),
(140, 'tere'),
(141, 'tető'),
(142, 'turistaház'),
(143, 'téli kikötő'),
(144, 'tér'),
(145, 'tömb'),
(146, 'udvar'),
(147, 'utak'),
(148, 'utca'),
(149, 'utcája'),
(150, 'vadaskert'),
(151, 'vadászház'),
(152, 'vasúti megálló'),
(153, 'vasúti őrház'),
(154, 'vasútsor'),
(155, 'vasútállomás'),
(156, 'vezetőút'),
(157, 'villasor'),
(158, 'vágóhíd'),
(159, 'vár'),
(160, 'várköz'),
(161, 'város'),
(162, 'vízmű'),
(163, 'völgy'),
(164, 'zsilip'),
(165, 'zug'),
(166, 'állat és növ.kert'),
(167, 'állomás'),
(168, 'árnyék'),
(169, 'árok'),
(170, 'átjáró'),
(171, 'őrház'),
(172, 'őrházak'),
(173, 'őrházlak'),
(174, 'út'),
(175, 'útja'),
(176, 'útőrház'),
(177, 'üdülő'),
(178, 'üdülő-part'),
(179, 'üdülő-sor'),
(180, 'üdülő-telep');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `areatypes`
--
ALTER TABLE `areatypes`
  ADD PRIMARY KEY (`id`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `area_types`
--
ALTER TABLE `areatypes`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=181;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
