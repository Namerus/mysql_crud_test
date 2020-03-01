-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Янв 20 2020 г., 01:43
-- Версия сервера: 5.7.28-0ubuntu0.18.04.4
-- Версия PHP: 7.2.24-0ubuntu0.18.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `test_db`
--

-- --------------------------------------------------------

--
-- Структура таблицы `cat_element`
--

CREATE TABLE `cat_element` (
  `ID` int(11) NOT NULL,
  `Section_ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Date_create` date DEFAULT NULL,
  `Date_modify` date DEFAULT NULL,
  `Type` varchar(255) DEFAULT NULL,
  `Description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `cat_element`
--

INSERT INTO `cat_element` (`ID`, `Section_ID`, `Name`, `Date_create`, `Date_modify`, `Type`, `Description`) VALUES
(1, 1, 'Футболка 1', '2020-01-17', '2020-01-19', NULL, 'Белого цвета'),
(2, 1, 'Куртка', '2020-01-17', NULL, 'Верхняя одежда', 'Материал: кожа');

-- --------------------------------------------------------

--
-- Структура таблицы `cat_section`
--

CREATE TABLE `cat_section` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Date_create` date DEFAULT NULL,
  `Date_modify` date DEFAULT NULL,
  `Description` text,
  `Parent_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `cat_section`
--

INSERT INTO `cat_section` (`ID`, `Name`, `Date_create`, `Date_modify`, `Description`, `Parent_ID`) VALUES
(1, 'Одежда', '2020-01-19', NULL, NULL, NULL),
(2, 'Электроника', '2020-01-17', NULL, 'Основной раздел с электроникой', NULL),
(4, 'Новое имя', '2020-01-18', NULL, 'Описание', 1),
(7, 'Подраздел 3 уровня', '2020-01-18', NULL, 'Описание', 4),
(8, 'Подраздел 4 уровня', '2020-01-18', NULL, 'Описание', 7),
(18, 'Ещё раздел', '2020-01-19', NULL, NULL, 7),
(20, 'Книги', '2020-01-19', NULL, NULL, NULL),
(23, 'Беллетристика', '2020-01-19', '2020-01-19', 's', 20),
(24, 'Раздел 1 уровня', '2020-01-19', NULL, NULL, NULL),
(25, 'Раздел 1 уровня', '2020-01-19', NULL, NULL, NULL),
(26, 'Раздел 1 уровня', '2020-01-19', NULL, NULL, NULL),
(27, 'Раздел, который не жалко удалить', '2020-01-19', NULL, NULL, NULL),
(28, 'Подраздел, который можно спокойно удалить', '2020-01-19', NULL, NULL, 27);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `cat_element`
--
ALTER TABLE `cat_element`
  ADD PRIMARY KEY (`ID`);

--
-- Индексы таблицы `cat_section`
--
ALTER TABLE `cat_section`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `cat_element`
--
ALTER TABLE `cat_element`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT для таблицы `cat_section`
--
ALTER TABLE `cat_section`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
