-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: localhost:3306
-- Létrehozás ideje: 2024. Jún 04. 21:23
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
-- Tábla szerkezet ehhez a táblához `countries`
--

CREATE TABLE `countries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `iso_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'iso kód',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'ország név'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- A tábla adatainak kiíratása `countries`
--

INSERT INTO `countries` (`id`, `iso_code`, `name`) VALUES
(1, 'AFG', 'Afghanistan'),
(2, 'ALA', 'Aland Islands'),
(3, 'ALB', 'Albania'),
(4, 'DZA', 'Algeria'),
(5, 'ASM', 'American Samoa'),
(6, 'AND', 'Andorra'),
(7, 'AGO', 'Angola'),
(8, 'AIA', 'Anguilla'),
(9, 'ATA', 'Antarctica'),
(10, 'ATG', 'Antigua and Barbuda'),
(11, 'ARG', 'Argentina'),
(12, 'ARM', 'Armenia'),
(13, 'ABW', 'Aruba'),
(14, 'AUS', 'Australia'),
(15, 'AUT', 'Austria'),
(16, 'AZE', 'Azerbaijan'),
(17, 'BHS', 'Bahamas'),
(18, 'BHR', 'Bahrain'),
(19, 'BGD', 'Bangladesh'),
(20, 'BRB', 'Barbados'),
(21, 'BLR', 'Belarus'),
(22, 'BEL', 'Belgium'),
(23, 'BLZ', 'Belize'),
(24, 'BEN', 'Benin'),
(25, 'BMU', 'Bermuda'),
(26, 'BTN', 'Bhutan'),
(27, 'BOL', 'Bolivia (Plurinational State of)'),
(28, 'BES', 'Bonaire, Sint Eustatius and Saba'),
(29, 'BIH', 'Bosnia and Herzegovina'),
(30, 'BWA', 'Botswana'),
(31, 'BVT', 'Bouvet Island'),
(32, 'BRA', 'Brazil'),
(33, 'IOT', 'British Indian Ocean Territory'),
(34, 'BRN', 'Brunei Darussalam'),
(35, 'BGR', 'Bulgaria'),
(36, 'BFA', 'Burkina Faso'),
(37, 'BDI', 'Burundi'),
(38, 'CPV', 'Cabo Verde'),
(39, 'KHM', 'Cambodia'),
(40, 'CMR', 'Cameroon'),
(41, 'CAN', 'Canada'),
(42, 'CYM', 'Cayman Islands'),
(43, 'CAF', 'Central African Republic'),
(44, 'TCD', 'Chad'),
(45, 'CHL', 'Chile'),
(46, 'CHN', 'China'),
(47, 'CXR', 'Christmas Island'),
(48, 'CCK', 'Cocos (Keeling) Islands'),
(49, 'COL', 'Colombia'),
(50, 'COM', 'Comoros'),
(51, 'COG', 'Congo'),
(52, 'COD', 'Congo, Democratic Republic of the'),
(53, 'COK', 'Cook Islands'),
(54, 'CRI', 'Costa Rica'),
(55, 'CIV', 'Côte d\'Ivoire'),
(56, 'HRV', 'Croatia'),
(57, 'CUB', 'Cuba'),
(58, 'CUW', 'Curaçao'),
(59, 'CYP', 'Cyprus'),
(60, 'CZE', 'Czechia'),
(61, 'DNK', 'Denmark'),
(62, 'DJI', 'Djibouti'),
(63, 'DMA', 'Dominica'),
(64, 'DOM', 'Dominican Republic'),
(65, 'ECU', 'Ecuador'),
(66, 'EGY', 'Egypt'),
(67, 'SLV', 'El Salvador'),
(68, 'GNQ', 'Equatorial Guinea'),
(69, 'ERI', 'Eritrea'),
(70, 'EST', 'Estonia'),
(71, 'SWZ', 'Eswatini'),
(72, 'ETH', 'Ethiopia'),
(73, 'FLK', 'Falkland Islands (Malvinas)'),
(74, 'FRO', 'Faroe Islands'),
(75, 'FJI', 'Fiji'),
(76, 'FIN', 'Finland'),
(77, 'FRA', 'France'),
(78, 'GUF', 'French Guiana'),
(79, 'PYF', 'French Polynesia'),
(80, 'ATF', 'French Southern Territories'),
(81, 'GAB', 'Gabon'),
(82, 'GMB', 'Gambia'),
(83, 'GEO', 'Georgia'),
(84, 'DEU', 'Germany'),
(85, 'GHA', 'Ghana'),
(86, 'GIB', 'Gibraltar'),
(87, 'GRC', 'Greece'),
(88, 'GRL', 'Greenland'),
(89, 'GRD', 'Grenada'),
(90, 'GLP', 'Guadeloupe'),
(91, 'GUM', 'Guam'),
(92, 'GTM', 'Guatemala'),
(93, 'GGY', 'Guernsey'),
(94, 'GIN', 'Guinea'),
(95, 'GNB', 'Guinea-Bissau'),
(96, 'GUY', 'Guyana'),
(97, 'HTI', 'Haiti'),
(98, 'HMD', 'Heard Island and McDonald Islands'),
(99, 'VAT', 'Holy See'),
(100, 'HND', 'Honduras'),
(101, 'HKG', 'Hong Kong'),
(102, 'HUN', 'Magyarország'),
(103, 'ISL', 'Iceland'),
(104, 'IND', 'India'),
(105, 'IDN', 'Indonesia'),
(106, 'IRN', 'Iran (Islamic Republic of)'),
(107, 'IRQ', 'Iraq'),
(108, 'IRL', 'Ireland'),
(109, 'IMN', 'Isle of Man'),
(110, 'ISR', 'Israel'),
(111, 'ITA', 'Italy'),
(112, 'JAM', 'Jamaica'),
(113, 'JPN', 'Japan'),
(114, 'JEY', 'Jersey'),
(115, 'JOR', 'Jordan'),
(116, 'KAZ', 'Kazakhstan'),
(117, 'KEN', 'Kenya'),
(118, 'KIR', 'Kiribati'),
(119, 'PRK', 'Korea (Democratic People\'s Republic of)'),
(120, 'KOR', 'Korea, Republic of'),
(121, 'KWT', 'Kuwait'),
(122, 'KGZ', 'Kyrgyzstan'),
(123, 'LAO', 'Lao People\'s Democratic Republic'),
(124, 'LVA', 'Latvia'),
(125, 'LBN', 'Lebanon'),
(126, 'LSO', 'Lesotho'),
(127, 'LBR', 'Liberia'),
(128, 'LBY', 'Libya'),
(129, 'LIE', 'Liechtenstein'),
(130, 'LTU', 'Lithuania'),
(131, 'LUX', 'Luxembourg'),
(132, 'MAC', 'Macao'),
(133, 'MDG', 'Madagascar'),
(134, 'MWI', 'Malawi'),
(135, 'MYS', 'Malaysia'),
(136, 'MDV', 'Maldives'),
(137, 'MLI', 'Mali'),
(138, 'MLT', 'Malta'),
(139, 'MHL', 'Marshall Islands'),
(140, 'MTQ', 'Martinique'),
(141, 'MRT', 'Mauritania'),
(142, 'MUS', 'Mauritius'),
(143, 'MYT', 'Mayotte'),
(144, 'MEX', 'Mexico'),
(145, 'FSM', 'Micronesia (Federated States of)'),
(146, 'MDA', 'Moldova, Republic of'),
(147, 'MCO', 'Monaco'),
(148, 'MNG', 'Mongolia'),
(149, 'MNE', 'Montenegro'),
(150, 'MSR', 'Montserrat'),
(151, 'MAR', 'Morocco'),
(152, 'MOZ', 'Mozambique'),
(153, 'MMR', 'Myanmar'),
(154, 'NAM', 'Namibia'),
(155, 'NRU', 'Nauru'),
(156, 'NPL', 'Nepal'),
(157, 'NLD', 'Netherlands'),
(158, 'NCL', 'New Caledonia'),
(159, 'NZL', 'New Zealand'),
(160, 'NIC', 'Nicaragua'),
(161, 'NER', 'Niger'),
(162, 'NGA', 'Nigeria'),
(163, 'NIU', 'Niue'),
(164, 'NFK', 'Norfolk Island'),
(165, 'MKD', 'North Macedonia'),
(166, 'MNP', 'Northern Mariana Islands'),
(167, 'NOR', 'Norway'),
(168, 'OMN', 'Oman'),
(169, 'PAK', 'Pakistan'),
(170, 'PLW', 'Palau'),
(171, 'PSE', 'Palestine, State of'),
(172, 'PAN', 'Panama'),
(173, 'PNG', 'Papua New Guinea'),
(174, 'PRY', 'Paraguay'),
(175, 'PER', 'Peru'),
(176, 'PHL', 'Philippines'),
(177, 'PCN', 'Pitcairn'),
(178, 'POL', 'Poland'),
(179, 'PRT', 'Portugal'),
(180, 'PRI', 'Puerto Rico'),
(181, 'QAT', 'Qatar'),
(182, 'REU', 'Réunion'),
(183, 'ROU', 'Romania'),
(184, 'RUS', 'Russian Federation'),
(185, 'RWA', 'Rwanda'),
(186, 'BLM', 'Saint Barthélemy'),
(187, 'SHN', 'Saint Helena, Ascension and Tristan da Cunha'),
(188, 'KNA', 'Saint Kitts and Nevis'),
(189, 'LCA', 'Saint Lucia'),
(190, 'MAF', 'Saint Martin (French part)'),
(191, 'SPM', 'Saint Pierre and Miquelon'),
(192, 'VCT', 'Saint Vincent and the Grenadines'),
(193, 'WSM', 'Samoa'),
(194, 'SMR', 'San Marino'),
(195, 'STP', 'Sao Tome and Principe'),
(196, 'SAU', 'Saudi Arabia'),
(197, 'SEN', 'Senegal'),
(198, 'SRB', 'Serbia'),
(199, 'SYC', 'Seychelles'),
(200, 'SLE', 'Sierra Leone'),
(201, 'SGP', 'Singapore'),
(202, 'SXM', 'Sint Maarten (Dutch part)'),
(203, 'SVK', 'Slovakia'),
(204, 'SVN', 'Slovenia'),
(205, 'SLB', 'Solomon Islands'),
(206, 'SOM', 'Somalia'),
(207, 'ZAF', 'South Africa'),
(208, 'SGS', 'South Georgia and the South Sandwich Islands'),
(209, 'SSD', 'South Sudan'),
(210, 'ESP', 'Spain'),
(211, 'LKA', 'Sri Lanka'),
(212, 'SDN', 'Sudan'),
(213, 'SUR', 'Suriname'),
(214, 'SJM', 'Svalbard and Jan Mayen'),
(215, 'SWE', 'Sweden'),
(216, 'CHE', 'Switzerland'),
(217, 'SYR', 'Syrian Arab Republic'),
(218, 'TWN', 'Taiwan, Province of China'),
(219, 'TJK', 'Tajikistan'),
(220, 'TZA', 'Tanzania, United Republic of'),
(221, 'THA', 'Thailand'),
(222, 'TLS', 'Timor-Leste'),
(223, 'TGO', 'Togo'),
(224, 'TKL', 'Tokelau'),
(225, 'TON', 'Tonga'),
(226, 'TTO', 'Trinidad and Tobago'),
(227, 'TUN', 'Tunisia'),
(228, 'TUR', 'Turkey'),
(229, 'TKM', 'Turkmenistan'),
(230, 'TCA', 'Turks and Caicos Islands'),
(231, 'TUV', 'Tuvalu'),
(232, 'UGA', 'Uganda'),
(233, 'UKR', 'Ukraine'),
(234, 'ARE', 'United Arab Emirates'),
(235, 'GBR', 'United Kingdom of Great Britain and Northern Ireland'),
(236, 'USA', 'United States of America'),
(237, 'UMI', 'United States Minor Outlying Islands'),
(238, 'URY', 'Uruguay'),
(239, 'UZB', 'Uzbekistan'),
(240, 'VUT', 'Vanuatu'),
(241, 'VEN', 'Venezuela (Bolivarian Republic of)'),
(242, 'VNM', 'Viet Nam'),
(243, 'VGB', 'Virgin Islands (British)'),
(244, 'VIR', 'Virgin Islands (U.S.)'),
(245, 'WLF', 'Wallis and Futuna'),
(246, 'ESH', 'Western Sahara'),
(247, 'YEM', 'Yemen'),
(248, 'ZMB', 'Zambia'),
(249, 'ZWE', 'Zimbabwe');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=250;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
