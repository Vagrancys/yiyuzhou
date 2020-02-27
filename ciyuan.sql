-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2017-10-30 07:42:20
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `y`
--

-- --------------------------------------------------------

--
-- 表的结构 `admin_front`
--
-- 创建时间： 2017-10-30 05:56:34
--

CREATE TABLE `admin_front` (
  `id` int(30) NOT NULL COMMENT '自增id',
  `admin_id` int(11) NOT NULL COMMENT '前端管理员id',
  `admin_name` varchar(255) NOT NULL COMMENT '管理员名字',
  `admin_level` varchar(255) NOT NULL COMMENT '前端管理员等级',
  `admin_page` int(11) NOT NULL COMMENT '管理区域',
  `time` varchar(255) NOT NULL COMMENT '注册时间',
  `static` int(11) NOT NULL DEFAULT '1' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `admin_front`
--

INSERT INTO `admin_front` (`id`, `admin_id`, `admin_name`, `admin_level`, `admin_page`, `time`, `static`) VALUES
(1, 1, '流浪中', 'G', 0, '1509008899', 1),
(2, 18, '萝莉', 'G', 8, '1509088234', 1);

-- --------------------------------------------------------

--
-- 表的结构 `admin_static`
--
-- 创建时间： 2017-10-30 05:57:18
--

CREATE TABLE `admin_static` (
  `id` int(30) NOT NULL COMMENT 'id',
  `admin_id` int(30) NOT NULL COMMENT '用户ID',
  `time` varchar(30) NOT NULL COMMENT '刚才登入时间',
  `ip` varchar(30) NOT NULL COMMENT '登入ip',
  `go_time` int(11) NOT NULL COMMENT '过去登录时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `admin_user`
--
-- 创建时间： 2017-10-03 06:12:40
--

CREATE TABLE `admin_user` (
  `id` int(30) NOT NULL COMMENT '自增id',
  `admin_name` varchar(255) NOT NULL COMMENT '管理员名字',
  `admin_password` varchar(255) NOT NULL COMMENT '管理员密码',
  `sex` int(10) NOT NULL DEFAULT '1' COMMENT '性别',
  `phone` int(30) NOT NULL COMMENT '手机',
  `email` int(30) NOT NULL COMMENT '邮箱',
  `time` varchar(255) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `group_id` varchar(255) NOT NULL COMMENT '角色id',
  `static` int(11) NOT NULL DEFAULT '1' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `admin_user`
--

INSERT INTO `admin_user` (`id`, `admin_name`, `admin_password`, `sex`, `phone`, `email`, `time`, `group_id`, `static`) VALUES
(1, '流浪中', '6af93fa45cfc39e697ee658d2dc8c25f', 0, 11, 111, '1507011369', '', 1);

-- --------------------------------------------------------

--
-- 表的结构 `article`
--
-- 创建时间： 2017-09-30 10:45:28
--

CREATE TABLE `article` (
  `article_id` int(11) NOT NULL COMMENT '自增id',
  `title` text NOT NULL COMMENT '标题',
  `brief` varchar(255) NOT NULL COMMENT '简易标题',
  `fid` int(11) NOT NULL COMMENT '分类id',
  `lid` int(11) NOT NULL COMMENT '类型id',
  `keywords` varchar(255) NOT NULL COMMENT '关键词',
  `abstract` text NOT NULL COMMENT '摘要',
  `author` text NOT NULL COMMENT '作者',
  `article_time` int(30) NOT NULL COMMENT '时间',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '状态',
  `editorValue` varchar(255) NOT NULL COMMENT '详细内容'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `article_column`
--
-- 创建时间： 2017-07-31 08:27:25
--

CREATE TABLE `article_column` (
  `id` int(11) NOT NULL COMMENT '自增id',
  `name` varchar(255) NOT NULL COMMENT '名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `article_type`
--
-- 创建时间： 2017-07-31 08:28:11
--

CREATE TABLE `article_type` (
  `id` int(11) NOT NULL COMMENT '自增id',
  `name` varchar(255) NOT NULL COMMENT '名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `auth_group`
--
-- 创建时间： 2017-10-30 05:59:06
--

CREATE TABLE `auth_group` (
  `id` int(11) NOT NULL COMMENT '自增id',
  `title` char(100) NOT NULL COMMENT '名称',
  `status` int(8) NOT NULL DEFAULT '1' COMMENT '状态',
  `rules` varchar(255) NOT NULL COMMENT '管理的项目',
  `item` varchar(255) NOT NULL COMMENT '要消失的项目',
  `text` varchar(255) NOT NULL COMMENT '描述'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `auth_group_access`
--
-- 创建时间： 2017-10-30 05:59:44
--

CREATE TABLE `auth_group_access` (
  `id` int(30) NOT NULL COMMENT '自增id',
  `uid` int(8) NOT NULL COMMENT '管理员id',
  `group_id` int(8) NOT NULL COMMENT '职务id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `auth_power`
--
-- 创建时间： 2017-10-03 06:45:14
--

CREATE TABLE `auth_power` (
  `id` int(11) NOT NULL COMMENT '自增id',
  `title` varchar(255) NOT NULL COMMENT '权限名称',
  `level` varchar(255) NOT NULL COMMENT '权限级别',
  `name` varchar(255) NOT NULL COMMENT '权限级别位置',
  `remark` varchar(255) NOT NULL COMMENT '描述',
  `parent_id` int(11) NOT NULL COMMENT '上级id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `front_auth_group`
--
-- 创建时间： 2017-10-30 06:00:44
--

CREATE TABLE `front_auth_group` (
  `id` int(11) NOT NULL COMMENT '自增id',
  `title` char(100) NOT NULL COMMENT '前端职务名称',
  `status` int(8) NOT NULL DEFAULT '1' COMMENT '状态',
  `rules` varchar(255) NOT NULL COMMENT '前端管理项目',
  `item` varchar(255) NOT NULL COMMENT '要消失的项目',
  `text` varchar(255) NOT NULL COMMENT '描述'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `front_auth_group_access`
--
-- 创建时间： 2017-10-30 06:01:35
--

CREATE TABLE `front_auth_group_access` (
  `id` int(30) NOT NULL COMMENT '自增id',
  `uid` int(8) NOT NULL COMMENT '前端管理员id',
  `group_id` int(8) NOT NULL COMMENT '前端职务id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `front_auth_power`
--
-- 创建时间： 2017-10-26 07:59:23
--

CREATE TABLE `front_auth_power` (
  `id` int(11) NOT NULL COMMENT '自增id',
  `title` varchar(255) NOT NULL COMMENT '权限名称',
  `level` varchar(255) NOT NULL COMMENT '权限级别',
  `name` varchar(255) NOT NULL COMMENT '权限级别位置',
  `remark` varchar(255) NOT NULL COMMENT '描述',
  `parent_id` int(11) NOT NULL COMMENT '上级id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `help_classify`
--
-- 创建时间： 2017-10-30 06:02:02
--

CREATE TABLE `help_classify` (
  `id` int(11) NOT NULL,
  `helpUid` int(11) NOT NULL COMMENT '副帮助id',
  `name` varchar(255) NOT NULL COMMENT '帮助分类名'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `help_classify`
--

INSERT INTO `help_classify` (`id`, `helpUid`, `name`) VALUES
(2, 0, '快速了解次元'),
(3, 2, '次元发张'),
(4, 0, '财富相关'),
(5, 4, '次元币相关'),
(6, 0, '搜索相关');

-- --------------------------------------------------------

--
-- 表的结构 `help_content`
--
-- 创建时间： 2017-10-30 06:02:38
--

CREATE TABLE `help_content` (
  `id` int(11) NOT NULL,
  `classifyId` int(11) NOT NULL COMMENT '分类',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `text` varchar(255) NOT NULL COMMENT '内容'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `help_content`
--

INSERT INTO `help_content` (`id`, `classifyId`, `title`, `text`) VALUES
(2, 3, '资源形式', '&lt;p&gt;在线，百度云，迅雷&lt;/p&gt;'),
(3, 5, '次元币是什么？', '次元币是次元平台通用的虚拟货币，可以开通次元平台内的各种方便和服务'),
(4, 6, '如何使用次元搜索？', '在次元平台内搜索框内输入关键词即可搜索。');

-- --------------------------------------------------------

--
-- 表的结构 `manage_guide`
--
-- 创建时间： 2017-10-30 06:04:37
--

CREATE TABLE `manage_guide` (
  `guide_id` int(11) NOT NULL,
  `guide_index` int(11) NOT NULL COMMENT '推荐的主专题',
  `guide_video` int(11) NOT NULL COMMENT '推荐的作品',
  `guide_time` int(11) NOT NULL COMMENT '推荐期限'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `manage_guide`
--

INSERT INTO `manage_guide` (`guide_id`, `guide_index`, `guide_video`, `guide_time`) VALUES
(2, 0, 1, 1507960737),
(3, 13, 2, 1507961078),
(4, 13, 0, 1507961178),
(5, 13, 1, 1507961349);

-- --------------------------------------------------------

--
-- 表的结构 `manage_guides`
--
-- 创建时间： 2017-10-30 06:06:01
--

CREATE TABLE `manage_guides` (
  `guides_id` int(11) NOT NULL,
  `guides_index` int(11) NOT NULL COMMENT '候补推荐专题',
  `guides_video` int(11) NOT NULL COMMENT '候补推荐作品',
  `guides_time` int(11) NOT NULL COMMENT '候补添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `manage_push`
--
-- 创建时间： 2017-10-13 15:37:43
--

CREATE TABLE `manage_push` (
  `push_id` int(11) NOT NULL,
  `push_index` int(11) NOT NULL COMMENT '区分主副',
  `push_video` int(11) NOT NULL COMMENT '上推举id',
  `push_time` int(11) NOT NULL COMMENT '时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `manage_push`
--

INSERT INTO `manage_push` (`push_id`, `push_index`, `push_video`, `push_time`) VALUES
(2, 0, 0, 1507959847),
(3, 0, 1, 1507959905),
(4, 0, 2, 1507959913),
(5, 13, 2, 1507961045),
(6, 13, 0, 1507961219),
(7, 13, 1, 1507961315);

-- --------------------------------------------------------

--
-- 表的结构 `manage_push_push`
--
-- 创建时间： 2017-10-13 15:36:19
--

CREATE TABLE `manage_push_push` (
  `push_id` int(11) NOT NULL,
  `pushs_video` int(11) NOT NULL COMMENT '推举作品',
  `pushs_index` int(11) NOT NULL COMMENT '要上的主副',
  `pushs_time` int(11) NOT NULL COMMENT '推举时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `member`
--
-- 创建时间： 2017-10-25 08:07:11
--

CREATE TABLE `member` (
  `id` int(11) NOT NULL COMMENT '自增id',
  `name` varchar(255) NOT NULL COMMENT '会员名字',
  `nickname` text NOT NULL COMMENT '昵称',
  `image` varchar(255) NOT NULL COMMENT '头像',
  `level` varchar(255) NOT NULL DEFAULT 'G' COMMENT '会员等级',
  `jurisdiction` int(11) NOT NULL DEFAULT '0' COMMENT '权限等级',
  `money` int(11) NOT NULL DEFAULT '0' COMMENT '积分',
  `money_apport` int(11) NOT NULL DEFAULT '0' COMMENT '钱的幻像',
  `sex` int(11) NOT NULL DEFAULT '1' COMMENT '性别',
  `phone` varchar(255) NOT NULL COMMENT '手机',
  `email` varchar(255) NOT NULL COMMENT '邮箱',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `dizhi` varchar(255) NOT NULL COMMENT '地址',
  `time` int(11) NOT NULL COMMENT '加入时间',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '状态',
  `activation` int(10) NOT NULL DEFAULT '0' COMMENT '邮箱激活状态',
  `code` varchar(255) NOT NULL COMMENT '邮箱激活码',
  `autograph` text NOT NULL COMMENT '会员签名',
  `notice` text NOT NULL COMMENT '公告'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `member`
--

INSERT INTO `member` (`id`, `name`, `nickname`, `image`, `level`, `jurisdiction`, `money`, `money_apport`, `sex`, `phone`, `email`, `password`, `dizhi`, `time`, `status`, `activation`, `code`, `autograph`, `notice`) VALUES
(1, '流浪中', '', '59c0ec1ee85ea.jpeg', 'G', 150, 35, 0, 1, '0', '123@163.com', '6af93fa45cfc39e697ee658d2dc8c25f', '', 1506695271, 0, 0, '', '随便说说！', '随便说说'),
(2, '萝莉', '', '', 'G', 0, 0, 0, 1, '', '18050829067@163.com', 'e10adc3949ba59abbe56e057f20f883e', '', 1505206155, 1, 1, 'f9bc6e12', '', '');

-- --------------------------------------------------------

--
-- 表的结构 `member_del`
--
-- 创建时间： 2017-09-29 14:36:56
--

CREATE TABLE `member_del` (
  `id` int(11) NOT NULL COMMENT '自增id',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `nickname` text NOT NULL COMMENT '昵称',
  `image` varchar(255) NOT NULL COMMENT '头像',
  `level` varchar(255) NOT NULL COMMENT '等级',
  `jurisdiction` int(30) NOT NULL COMMENT '权限等级',
  `jifen` int(30) NOT NULL COMMENT '积分',
  `time` int(11) NOT NULL COMMENT '时间',
  `phone` varchar(255) NOT NULL COMMENT '手机',
  `email` varchar(255) NOT NULL COMMENT '邮箱',
  `activation` int(11) NOT NULL DEFAULT '0' COMMENT '邮箱激活状态',
  `code` varchar(255) NOT NULL COMMENT '激活码',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `dizhi` varchar(255) NOT NULL COMMENT '地址',
  `sex` int(11) NOT NULL COMMENT '性别',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '状态',
  `text` varchar(255) NOT NULL COMMENT '描述'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `member_integral`
--
-- 创建时间： 2017-08-03 07:09:49
--

CREATE TABLE `member_integral` (
  `id` int(11) NOT NULL COMMENT '自增id',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `time` varchar(255) NOT NULL COMMENT '周期时间',
  `number` int(11) NOT NULL COMMENT '次数',
  `money` varchar(255) NOT NULL COMMENT '钱'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `member_integral`
--

INSERT INTO `member_integral` (`id`, `name`, `time`, `number`, `money`) VALUES
(1, '签到', '一天', 1, '1毛');

-- --------------------------------------------------------

--
-- 表的结构 `member_level`
--
-- 创建时间： 2017-08-03 06:32:28
--

CREATE TABLE `member_level` (
  `id` int(11) NOT NULL COMMENT '自增id',
  `name` varchar(255) NOT NULL COMMENT '等级名称',
  `user` varchar(255) NOT NULL COMMENT '用户等级',
  `require` varchar(255) NOT NULL COMMENT '要求'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `member_level`
--

INSERT INTO `member_level` (`id`, `name`, `user`, `require`) VALUES
(1, 'G级战士', '一元战斗力', '身价一元'),
(2, 'F级战士', '十元战斗力', '身价十元'),
(3, 'E级战士', '百元战斗力', '身价百元'),
(4, 'D级战士', '千元战斗力', '身价千元'),
(5, 'C级战士', '万元战斗力', '身价万元'),
(6, 'B级战士', '十万元战斗力', '身价十万元'),
(7, 'A级战士', '百万元战斗力', '身价百万元'),
(8, 'S级战士', '千万元战斗力', '身价千万元'),
(9, 'SS级战士', '亿元战斗力', '身价亿元'),
(10, 'SSS级战士', '十亿元战斗力', '身价十亿元'),
(11, 'EX级战士', '百亿元战斗力', '身价百亿元');

-- --------------------------------------------------------

--
-- 表的结构 `member_sign`
--
-- 创建时间： 2017-09-21 07:43:22
--

CREATE TABLE `member_sign` (
  `id` int(11) NOT NULL,
  `sign_uid` int(11) NOT NULL COMMENT '会员id',
  `sign_time` int(11) NOT NULL COMMENT '签到时间',
  `sign_number` int(11) NOT NULL DEFAULT '1' COMMENT '签到次数',
  `sign_con` int(11) NOT NULL DEFAULT '1' COMMENT '连续签到',
  `sign_month` int(11) NOT NULL DEFAULT '1' COMMENT '连续月签到',
  `sign_week` int(11) NOT NULL DEFAULT '1' COMMENT '连续周签到',
  `year` int(11) NOT NULL COMMENT '年',
  `month` int(11) NOT NULL COMMENT '月',
  `day` int(11) NOT NULL COMMENT '日'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `news`
--
-- 创建时间： 2017-10-30 06:07:56
--

CREATE TABLE `news` (
  `news_id` int(11) NOT NULL,
  `userId` int(11) NOT NULL COMMENT '用户id',
  `static` int(11) NOT NULL COMMENT '状态',
  `news_Classify` int(11) NOT NULL COMMENT '消息分类',
  `news_Time` int(11) NOT NULL COMMENT '消息时间',
  `news_Text` text NOT NULL COMMENT '消息内容'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `news_classify`
--
-- 创建时间： 2017-09-22 16:11:29
--

CREATE TABLE `news_classify` (
  `id` int(11) NOT NULL,
  `newsUid` int(11) NOT NULL COMMENT '父分类消息id',
  `newsName` varchar(255) NOT NULL COMMENT '消息分类名字'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `news_classify`
--

INSERT INTO `news_classify` (`id`, `newsUid`, `newsName`) VALUES
(1, 0, '审核'),
(2, 0, '用户举报'),
(3, 0, '用户评论举报'),
(4, 0, '私信'),
(5, 0, '管理员通知'),
(6, 0, '作品举报'),
(7, 0, '公告'),
(8, 0, '日常事务'),
(9, 0, '评论');

-- --------------------------------------------------------

--
-- 表的结构 `news_comment`
--
-- 创建时间： 2017-10-16 11:07:08
--

CREATE TABLE `news_comment` (
  `comment_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL COMMENT '评论主',
  `video_id` int(11) NOT NULL COMMENT '评论作品',
  `news_classify` int(11) NOT NULL COMMENT '评论类型',
  `news_time` int(11) NOT NULL COMMENT '评论时间',
  `news_static` int(11) NOT NULL DEFAULT '1' COMMENT '评论状态',
  `news_user` int(11) NOT NULL COMMENT '评论回复主',
  `news_text` text NOT NULL COMMENT '评论内容',
  `news_number` int(11) NOT NULL COMMENT '楼层数'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `news_comment_recovery`
--
-- 创建时间： 2017-10-30 06:10:49
--

CREATE TABLE `news_comment_recovery` (
  `comment_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL COMMENT '评论主',
  `video_id` int(11) NOT NULL COMMENT '评论作品',
  `news_classify` int(11) NOT NULL COMMENT '评论分类',
  `news_time` int(11) NOT NULL COMMENT '评论时间',
  `news_user` int(11) NOT NULL COMMENT '评论回复主',
  `news_text` text NOT NULL COMMENT '评论内容',
  `news_static` int(11) NOT NULL COMMENT '状态',
  `news_number` int(11) NOT NULL COMMENT '评论楼层'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `news_feedback`
--
-- 创建时间： 2017-10-25 03:28:04
--

CREATE TABLE `news_feedback` (
  `view_id` int(11) NOT NULL COMMENT '自增id',
  `feedback_name` varchar(255) NOT NULL COMMENT '名字',
  `time` int(11) NOT NULL COMMENT '意见时间',
  `feedback_click` int(11) NOT NULL DEFAULT '0' COMMENT '点击',
  `feedback_tet` varchar(255) NOT NULL COMMENT '回复内容',
  `feedback_text` varchar(255) NOT NULL COMMENT '内容',
  `feedback_static` int(11) NOT NULL DEFAULT '0' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `news_new`
--
-- 创建时间： 2017-10-30 06:11:11
--

CREATE TABLE `news_new` (
  `news_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL COMMENT '用户id',
  `new_classify` int(11) NOT NULL COMMENT '类型',
  `user_id` int(11) NOT NULL COMMENT '消息接收人',
  `new_text` text NOT NULL COMMENT '消息内容',
  `time` int(11) NOT NULL COMMENT '消息时间',
  `static` int(11) NOT NULL COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `news_view`
--
-- 创建时间： 2017-10-30 06:12:40
--

CREATE TABLE `news_view` (
  `id` int(11) NOT NULL,
  `view_id` int(11) NOT NULL COMMENT '用户id',
  `view_view` int(11) NOT NULL COMMENT '支持意见id',
  `view_static` int(11) NOT NULL COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `overall_domain`
--
-- 创建时间： 2017-09-10 07:21:28
--

CREATE TABLE `overall_domain` (
  `id` int(11) NOT NULL,
  `second` varchar(255) NOT NULL COMMENT '二级域名设计'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `overall_email`
--
-- 创建时间： 2017-10-05 11:49:23
--

CREATE TABLE `overall_email` (
  `id` int(11) NOT NULL,
  `formname` varchar(255) NOT NULL COMMENT '发件人姓名',
  `form` varchar(255) NOT NULL COMMENT '发件人账号',
  `user` varchar(255) NOT NULL COMMENT '服务器账号',
  `password` varchar(255) NOT NULL COMMENT '服务器密码',
  `address` varchar(255) NOT NULL COMMENT '收件人邮箱地址',
  `title` varchar(255) NOT NULL COMMENT '标题'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `overall_email`
--

INSERT INTO `overall_email` (`id`, `formname`, `form`, `user`, `password`, `address`, `title`) VALUES
(1, '次元', '18559472896@163.com', '18559472896@163.com', 'wangzhihua519', '尊敬的用户', '[次元] Email 地址验证!');

-- --------------------------------------------------------

--
-- 表的结构 `overall_optimize`
--
-- 创建时间： 2017-09-10 07:11:34
--

CREATE TABLE `overall_optimize` (
  `id` int(11) NOT NULL,
  `lazyload` varchar(30) NOT NULL COMMENT '图片控制',
  `sessionclose` varchar(30) NOT NULL COMMENT 'session开关'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `overall_optimize`
--

INSERT INTO `overall_optimize` (`id`, `lazyload`, `sessionclose`) VALUES
(1, 'on', 'on');

-- --------------------------------------------------------

--
-- 表的结构 `overall_register`
--
-- 创建时间： 2017-10-30 06:13:17
--

CREATE TABLE `overall_register` (
  `id` int(30) NOT NULL,
  `static` varchar(255) NOT NULL COMMENT '注册状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `overall_register`
--

INSERT INTO `overall_register` (`id`, `static`) VALUES
(1, 'on');

-- --------------------------------------------------------

--
-- 表的结构 `overall_search`
--
-- 创建时间： 2017-09-10 07:24:32
--

CREATE TABLE `overall_search` (
  `id` int(11) NOT NULL,
  `search` varchar(255) NOT NULL COMMENT '开启搜索'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `overall_seo`
--
-- 创建时间： 2017-10-05 07:22:06
--

CREATE TABLE `overall_seo` (
  `id` int(30) NOT NULL COMMENT 'id',
  `portal` varchar(255) NOT NULL COMMENT '首页seo',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `keyword` varchar(255) NOT NULL COMMENT '关键词'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `overall_seo`
--

INSERT INTO `overall_seo` (`id`, `portal`, `title`, `keyword`) VALUES
(1, '次元，完善的资源内容，使用户迅速找到需要的资源，详细又完善！', '次元', '次元，视频，影视，动画，电影');

-- --------------------------------------------------------

--
-- 表的结构 `overall_upload`
--
-- 创建时间： 2017-10-05 10:21:20
--

CREATE TABLE `overall_upload` (
  `id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL COMMENT '图片大小',
  `video` varchar(255) NOT NULL COMMENT '视频大小',
  `file` varchar(255) NOT NULL COMMENT '文件大小'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `overall_upload`
--

INSERT INTO `overall_upload` (`id`, `image`, `video`, `file`) VALUES
(1, '2048000', '102400000', '51200000');

-- --------------------------------------------------------

--
-- 表的结构 `overall_user`
--
-- 创建时间： 2017-09-10 07:19:25
--

CREATE TABLE `overall_user` (
  `id` int(11) NOT NULL,
  `prompt` varchar(255) NOT NULL COMMENT '是否提示用户审核'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `system_category`
--
-- 创建时间： 2017-10-30 06:14:22
--

CREATE TABLE `system_category` (
  `id` int(30) NOT NULL COMMENT '自增id',
  `cid` varchar(30) NOT NULL COMMENT '上级目录id',
  `dyid` varchar(255) NOT NULL COMMENT '权限判断关键',
  `sb` varchar(30) NOT NULL COMMENT '图标',
  `module_name` varchar(255) NOT NULL COMMENT '标题名字',
  `module_di` varchar(255) NOT NULL COMMENT '地址'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `system_category`
--

INSERT INTO `system_category` (`id`, `cid`, `dyid`, `sb`, `module_name`, `module_di`) VALUES
(2, '0', 'system', '&#xe62e;', '系统模块', '0'),
(3, '2', '', '', '模块管理', 'system_module.html'),
(4, '0', 'article', '&#xe616;', '资讯模块', ''),
(5, '4', 'article', '', '公告管理', 'article_list.html'),
(8, '0', 'goods', '&#xe620;', '资源模块', ''),
(12, '0', 'comment', '&#xe622;', '评论模块', ''),
(13, '12', '', '', '评论列表', 'comment_list.html'),
(14, '12', '', '', '意见反馈', 'feedback_list.html'),
(15, '0', 'member', '', '用户模块', ''),
(16, '15', '', '', '会员列表', 'member_list.html'),
(17, '15', '', '', '删除的会员', 'member_del.html'),
(18, '15', '', '', '等级管理', 'member_level.html'),
(19, '15', '', '', '积分管理', 'member_integral.html'),
(23, '0', 'users', '&#xe62d;', '管理员模块', ''),
(24, '23', 'users', '', '管理员列表', 'admin_list.html'),
(25, '23', '', '', '角色管理', 'admin_role.html'),
(26, '23', '', '', '网站权限列表', 'admin_auth.html'),
(27, '0', '', '&#xe61a;', '系统统计模块', ''),
(29, '4', '', '', '类型管理', 'column_list.html'),
(30, '4', '', '', '分类管理', 'type_list.html'),
(32, '8', '', '', '视频管理', 'video_list.html'),
(35, '0', 'website', '&#xe625;', '全站模块', ''),
(36, '35', '', '', '站点信息管理', 'site_list.html'),
(37, '35', '', '', '注册管理', 'register_list.html'),
(38, '35', '', '', '优化管理', 'optimize_list.html'),
(39, '35', '', '', 'seo设置', 'seo_list.html'),
(40, '35', '', '', '域名设置', 'domain_list.html'),
(41, '35', '', '', '用户权限', 'users_list.html'),
(42, '35', '', '', '搜索设置', 'search_s_list.html'),
(43, '0', 'content', '&#xe63c;', '内容管理模块', ''),
(44, '43', '', '', '词语过滤', 'terms_list.html'),
(46, '43', '', '', '用户举报', 'report_list.html'),
(47, '43', '', '', '举报已处理', 'report_handle.html'),
(49, '8', '', '', '视频回收站', 'video_recycle.html'),
(50, '12', '', '', '评论回收站', 'comment_recovery.html'),
(51, '0', 'nav', '&#xe681;', '网站导航模块', ''),
(52, '51', '', '', '主页导航管理', 'nav.html'),
(53, '0', 'help', '&#xe633;', '帮助中心模块', ''),
(54, '53', '', '', '帮助分类', 'help_classify.html'),
(55, '53', '', '', '帮助内容', 'help_content.html'),
(56, '0', 'spl', '&#xe621;', '数据库模块', ''),
(57, '56', '', '', '数据库目录', 'mysql.html'),
(58, '15', '', '', '签到记录', 'admin_sign.html'),
(59, '35', '', '', '上传管理', 'upload_list.html'),
(60, '35', '', '', '邮箱管理', 'email_list.html'),
(61, '8', '', '', '播放器管理', 'player_list.html'),
(62, '0', 'manage', '&#xe61d;', '人工管理模块', ''),
(63, '62', 'manage', '', '主副推荐管理', 'push_list.html'),
(64, '62', 'manage', '', '推荐候补管理', 'pushs_list.html'),
(65, '62', 'manage', '', '导流管理', 'guide_list.html'),
(66, '62', 'manage', '', '导流候补管理', 'guides_list.html'),
(67, '15', 'member', '', '用户财富模块', 'member_coin.html'),
(68, '2', 'system', '', '网站资源模块', 'system_money.html'),
(69, '0', 'front', '&#xe61d;', '前端系统模块', ''),
(70, '69', 'front', '', '前端模块管理', 'front_list.html'),
(71, '69', 'front', '', '前端权限管理员', 'front_power.html'),
(72, '69', 'front', '', '前端角色管理', 'front_role.html'),
(73, '69', 'front', '', '前端权限列表', 'front_auth.html');

-- --------------------------------------------------------

--
-- 表的结构 `system_front`
--
-- 创建时间： 2017-10-30 06:15:36
--

CREATE TABLE `system_front` (
  `id` int(11) NOT NULL,
  `cid` int(11) NOT NULL COMMENT '前端上级目录',
  `dyid` varchar(255) NOT NULL COMMENT '前端权限判断关键',
  `sb` varchar(255) NOT NULL COMMENT '前端图标',
  `module_name` varchar(255) NOT NULL COMMENT '前端名称',
  `module_di` varchar(255) NOT NULL COMMENT '前端地址'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `system_front`
--

INSERT INTO `system_front` (`id`, `cid`, `dyid`, `sb`, `module_name`, `module_di`) VALUES
(2, 0, 'report', '&#xe6a7;', '举报中心', ''),
(4, 2, 'report', '', '举报作品审核', 'admins_video_static.html'),
(5, 2, 'report', '', '举报评论审核', 'admins_video_comment.html'),
(6, 0, 'video', '&#xe695;', '作品审核中心', ''),
(7, 6, 'video', '', '作品审核', 'admins_video.html');

-- --------------------------------------------------------

--
-- 表的结构 `system_money`
--
-- 创建时间： 2017-10-30 06:17:00
--

CREATE TABLE `system_money` (
  `money_id` int(11) NOT NULL,
  `money_name` varchar(255) NOT NULL COMMENT '财富类别名称',
  `money_number` int(11) NOT NULL COMMENT '财富类别数量',
  `money_static` int(11) NOT NULL DEFAULT '1' COMMENT '财富类别状态',
  `money_time` int(11) NOT NULL COMMENT '财富类别修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `system_money`
--

INSERT INTO `system_money` (`money_id`, `money_name`, `money_number`, `money_static`, `money_time`) VALUES
(1, '网站签到财富', 93, 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `user_browse`
--
-- 创建时间： 2017-10-30 06:18:52
--

CREATE TABLE `user_browse` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '浏览记录主',
  `data_id` int(11) NOT NULL COMMENT '浏览记录作品',
  `user_Time` int(11) NOT NULL COMMENT '浏览记录时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user_channel`
--
-- 创建时间： 2017-10-30 06:24:58
--

CREATE TABLE `user_channel` (
  `channel_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '创建主',
  `channel_name` varchar(255) NOT NULL COMMENT '作品集名称',
  `one_video` int(11) NOT NULL COMMENT '第一个作品',
  `Time` int(11) NOT NULL COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user_coin`
--
-- 创建时间： 2017-10-30 06:26:45
--

CREATE TABLE `user_coin` (
  `coin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '购买主',
  `video_id` int(11) NOT NULL COMMENT '购买作品的专题',
  `coin_time` int(11) NOT NULL COMMENT '购买时间',
  `coin` int(11) NOT NULL COMMENT '购买金币',
  `coin_data` int(11) NOT NULL COMMENT '购买作品',
  `users_id` int(11) NOT NULL COMMENT '接受主',
  `classify` int(11) NOT NULL COMMENT '购买类别'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user_collection`
--
-- 创建时间： 2017-10-30 06:27:10
--

CREATE TABLE `user_collection` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '收藏主',
  `data_id` int(11) NOT NULL COMMENT '收藏作品'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user_detail`
--
-- 创建时间： 2017-10-30 06:27:51
--

CREATE TABLE `user_detail` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '频道主',
  `channel_id` int(11) NOT NULL COMMENT '上级频道ID',
  `data_id` int(11) NOT NULL COMMENT '作品'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user_fans`
--
-- 创建时间： 2017-10-30 06:28:16
--

CREATE TABLE `user_fans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '关注主',
  `fans_id` int(11) NOT NULL COMMENT '粉丝ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `video`
--
-- 创建时间： 2017-09-23 07:01:58
--

CREATE TABLE `video` (
  `id` int(11) NOT NULL COMMENT '自增id',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `page` varchar(255) NOT NULL COMMENT '频道',
  `single` int(11) NOT NULL COMMENT '类型',
  `official` varchar(255) NOT NULL COMMENT '官网',
  `region` varchar(255) NOT NULL COMMENT '地区',
  `performer` varchar(255) NOT NULL COMMENT '演员',
  `correlation` varchar(255) NOT NULL COMMENT '相关',
  `user_id` int(11) NOT NULL COMMENT '投稿者',
  `time` int(11) NOT NULL COMMENT '投稿时间',
  `author` varchar(255) NOT NULL COMMENT '作者',
  `video_text` text NOT NULL COMMENT '描述'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `video`
--

INSERT INTO `video` (`id`, `title`, `page`, `single`, `official`, `region`, `performer`, `correlation`, `user_id`, `time`, `author`, `video_text`) VALUES
(1, '刀剑神域：序列之争', '8', 9, '无', '日本', '无', '无                                                                                                                                                                                                                                                          ', 1, 1507538392, '川原砾', '<p><img src=\"/images/video_images/1507538378695608.jpg\" title=\"1507538378695608.jpg\" alt=\"t01f3791c1194102051.webp.jpg\"/></p><p><span style=\"color: rgb(153, 153, 153); font-family: Arial, &quot;Microsoft Yahei&quot;, 微软雅黑, &quot;hiragino sans gb&quot;, simsun; font-size: 14px; background-color: rgb(255, 255, 255);\">2022年，天才编程者茅场晶彦所开发的世界最早的完全潜行专用装备设备《NERvGear》。 这个革命性的机器给VR（假想现实）世界带来了无限的可能性。那之后经过了4年。。。。。 《NERvGear》的后继品VR机为了对抗《AmuSphere》（第二代民用完全潜行机），发售了一个次世代的可穿戴设备《Augma》 替换了完全潜行机能，是一个对AR（增强现实）功能进行了最大限度扩大的最先进机种。 由于《Augma》在觉醒状态下也可以安全和便利地使用，因此一瞬间便在玩家当中传开了。 这个杀手级内容，被叫做《Ordinal Scale序列之争（OS）》，是《Augma》专用的ARMMO RPG。 亚斯娜和伙伴们会玩的这个游戏，桐人也准备参战了。。。。</span></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p>'),
(2, '龙珠超', '13', 14, '无', '日本', '无', '无', 1, 1507538757, '地冈公俊', '<p><img src=\"/images/video_images/1507538717430532.jpg\" title=\"1507538717430532.jpg\" alt=\"dy_8ba9013428515c265b3d45a7192db76b.jpg\"/></p><p><span style=\" font-family: 微软雅黑, Arial, sans-serif, 宋体; font-size: 12px;\">《龙珠》新系列动画的舞台为悟空与魔人布欧的壮绝战斗结束后，地球重新恢复和平之后发生的故事。与自漫长沉睡中觉醒的破坏神比鲁斯的相遇，加上曾经被人敬畏为“宇宙帝王”的弗利萨的复活，在这些接连迫近悟空等人的威胁之上，地球周边还发生了星球消失的不可思议现象，更有神秘的新角色“象帕”登场。</span></p>');

-- --------------------------------------------------------

--
-- 表的结构 `video_address`
--
-- 创建时间： 2017-09-23 06:30:15
--

CREATE TABLE `video_address` (
  `addressId` int(11) NOT NULL,
  `Location` varchar(255) NOT NULL COMMENT '播放器名',
  `Address` varchar(255) NOT NULL COMMENT '播放器地址'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `video_address`
--

INSERT INTO `video_address` (`addressId`, `Location`, `Address`) VALUES
(1, '爱奇艺', 'http://dispatcher.video.qiyi.com/disp/shareplayer.swf'),
(2, '野生播放器', '');

-- --------------------------------------------------------

--
-- 表的结构 `video_collect`
--
-- 创建时间： 2017-10-22 09:00:18
--

CREATE TABLE `video_collect` (
  `collectionId` int(11) NOT NULL,
  `Uid` int(11) NOT NULL COMMENT '父作品id',
  `Collect` text NOT NULL COMMENT '集数名',
  `Text` varchar(255) NOT NULL COMMENT '当前集数描述',
  `AddressUid` int(11) NOT NULL COMMENT '播放器id',
  `Line` varchar(255) NOT NULL COMMENT '播放参数',
  `Code` varchar(255) NOT NULL COMMENT '下载链接',
  `wedPan` varchar(255) NOT NULL COMMENT '网盘',
  `panCode` int(11) NOT NULL COMMENT '提取或密码',
  `Coin` int(11) NOT NULL COMMENT '价格',
  `Time` int(11) NOT NULL COMMENT '添加时间',
  `Static` int(11) NOT NULL DEFAULT '0' COMMENT '当前集数审核状态',
  `Sort` int(11) NOT NULL COMMENT '排序'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `video_collect`
--

INSERT INTO `video_collect` (`collectionId`, `Uid`, `Collect`, `Text`, `AddressUid`, `Line`, `Code`, `wedPan`, `panCode`, `Coin`, `Time`, `Static`, `Sort`) VALUES
(1, 1, '正片', '刀剑神域：序列之争完整版', 2, 'http://video.yingtu.co/0/531dc2a9-7acd-4ec2-bcff-2a5558af85f1.mp4', '无', '无', 0, 1, 1507538393, 1, 1),
(2, 2, '第109集', '悟空面对最强劲敌! 发出杀手锏元气弹!', 0, '无', '无', 'http://pan.baidu.com/s/1c2bDmoC', 0, 1, 1507538758, 1, 109),
(3, 2, '第110集', ' 孙悟空醒来! 觉醒者的新真髓!!', 0, '无', '无', 'http://pan.baidu.com/s/1c2bDmoC', 0, 0, 1507538758, 1, 110);

-- --------------------------------------------------------

--
-- 表的结构 `video_collect_del`
--
-- 创建时间： 2017-10-02 14:56:16
--

CREATE TABLE `video_collect_del` (
  `collectionId` int(11) NOT NULL DEFAULT '0',
  `Uid` int(11) NOT NULL COMMENT '父作品id',
  `Collect` text NOT NULL COMMENT '集数名',
  `Text` varchar(255) NOT NULL COMMENT '当前集数描述',
  `AddressUid` int(11) NOT NULL COMMENT '播放器id',
  `Line` varchar(255) NOT NULL COMMENT '播放参数',
  `Code` varchar(255) NOT NULL COMMENT '下载链接',
  `wedPan` varchar(255) NOT NULL COMMENT '网盘',
  `panCode` int(11) NOT NULL COMMENT '提取或密码',
  `Time` int(11) NOT NULL COMMENT '添加时间',
  `Static` int(11) NOT NULL DEFAULT '0' COMMENT '当前集数审核状态',
  `Sort` int(11) NOT NULL COMMENT '排序'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `video_data`
--
-- 创建时间： 2017-09-23 05:29:39
--

CREATE TABLE `video_data` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL COMMENT '作品参数',
  `videouid` int(11) NOT NULL COMMENT '作品总父级',
  `score` varchar(255) NOT NULL COMMENT '评分',
  `number` int(11) NOT NULL COMMENT '等级分',
  `level` varchar(255) NOT NULL COMMENT '等级',
  `click` int(11) NOT NULL COMMENT '观看数',
  `honor` int(11) NOT NULL COMMENT '荣誉'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `video_del`
--
-- 创建时间： 2017-10-30 06:29:17
--

CREATE TABLE `video_del` (
  `id` int(11) NOT NULL COMMENT '自增id',
  `title` varchar(255) NOT NULL COMMENT '删除掉的标题',
  `page` varchar(255) NOT NULL COMMENT '频道',
  `single` int(11) NOT NULL COMMENT '类型',
  `official` varchar(255) NOT NULL COMMENT '官网',
  `region` varchar(255) NOT NULL COMMENT '地区',
  `performer` varchar(255) NOT NULL COMMENT '演员',
  `correlation` varchar(255) NOT NULL COMMENT '相关',
  `user_id` int(11) NOT NULL COMMENT '投稿者',
  `time` int(11) NOT NULL COMMENT '投稿时间',
  `author` varchar(255) NOT NULL COMMENT '作者',
  `video_text` text NOT NULL COMMENT '描述'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `video_nav`
--
-- 创建时间： 2017-10-29 13:01:52
--

CREATE TABLE `video_nav` (
  `id` int(11) NOT NULL COMMENT 'id',
  `level` int(11) NOT NULL COMMENT '等级',
  `page` int(11) NOT NULL COMMENT '页面id',
  `single` int(11) NOT NULL COMMENT '单项id',
  `total` int(11) NOT NULL COMMENT '单项总导航',
  `vo` int(11) NOT NULL COMMENT '判断全站导航',
  `name` varchar(255) NOT NULL COMMENT '导航名称',
  `english` varchar(255) NOT NULL COMMENT '英文名'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `video_nav`
--

INSERT INTO `video_nav` (`id`, `level`, `page`, `single`, `total`, `vo`, `name`, `english`) VALUES
(1, 1, 0, 0, 0, 0, '首页导航', ''),
(2, 2, 1, 0, 0, 0, '电视剧', 'TV'),
(3, 3, 1, 2, 0, 0, '大陆剧', ''),
(4, 3, 1, 2, 0, 0, '港剧', ''),
(5, 3, 1, 2, 0, 0, '韩剧', ''),
(6, 3, 1, 2, 0, 0, '美剧', ''),
(7, 3, 1, 2, 0, 0, '日剧', ''),
(8, 2, 1, 0, 0, 0, '电影', 'film'),
(9, 3, 1, 8, 0, 0, '科幻', ''),
(10, 3, 1, 8, 0, 0, '战争', ''),
(11, 2, 1, 0, 0, 0, '综艺', 'variety'),
(12, 3, 1, 11, 0, 0, '内地综艺', ''),
(13, 2, 1, 0, 0, 0, '动漫', 'anime'),
(14, 3, 1, 13, 0, 0, '日漫', ''),
(15, 3, 1, 13, 0, 0, '国漫', ''),
(16, 3, 1, 13, 0, 0, '美漫', ''),
(17, 2, 1, 0, 0, 0, '搞笑', ''),
(19, 2, 8, 0, 0, 1, '电影', ''),
(22, 3, 19, 0, 0, 0, '地区', ''),
(23, 4, 0, 0, 22, 0, '内地', ''),
(24, 1, 0, 0, 0, 0, '排行榜导航', ''),
(25, 2, 24, 0, 0, 0, '全站榜', ''),
(26, 3, 24, 0, 0, 0, '全站', ''),
(27, 2, 2, 0, 0, 1, '电视剧', ''),
(30, 3, 27, 0, 0, 0, '地区', ''),
(31, 4, 0, 0, 30, 0, '古装', ''),
(32, 3, 27, 0, 0, 0, '年代', ''),
(33, 2, 11, 0, 0, 1, '综艺', ''),
(34, 2, 1, 0, 0, 0, '娱乐', ''),
(36, 3, 1, 34, 0, 0, '韩娱', ''),
(41, 3, 33, 0, 0, 0, '地区', ''),
(43, 4, 0, 0, 33, 0, '中型', ''),
(44, 2, 24, 0, 0, 0, '飙升榜', ''),
(45, 3, 24, 0, 0, 0, '电影', ''),
(46, 2, 1, 0, 0, 0, '直播', ''),
(47, 3, 1, 46, 0, 0, '美奴', ''),
(48, 2, 46, 0, 0, 1, '直播', ''),
(49, 3, 48, 0, 0, 0, '大型直播', ''),
(50, 4, 0, 0, 48, 0, '无敌', ''),
(51, 2, 34, 0, 0, 1, '娱乐', ''),
(52, 2, 13, 0, 0, 1, '动漫', ''),
(53, 2, 17, 0, 0, 1, '搞笑', '');

-- --------------------------------------------------------

--
-- 表的结构 `video_sort`
--
-- 创建时间： 2017-09-14 15:10:04
--

CREATE TABLE `video_sort` (
  `id` int(11) NOT NULL COMMENT '自增id',
  `mainsort` varchar(255) NOT NULL COMMENT '主分类id',
  `subsort` varchar(255) NOT NULL COMMENT '次分类id',
  `name` varchar(255) NOT NULL COMMENT '分类名'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `web_report`
--
-- 创建时间： 2017-10-30 06:33:40
--

CREATE TABLE `web_report` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '举报id',
  `report_class` int(11) NOT NULL COMMENT '举报类型',
  `report_video` int(11) NOT NULL COMMENT '举报作品名',
  `report_video_id` int(11) NOT NULL COMMENT '评论的id',
  `report_video_collect` int(11) NOT NULL COMMENT '作品下的集数',
  `report_text` varchar(255) NOT NULL COMMENT '举报内容',
  `report_tet` varchar(255) NOT NULL COMMENT '回复内容',
  `report_reason` varchar(255) NOT NULL COMMENT '举报理由',
  `report_static` int(11) NOT NULL DEFAULT '0' COMMENT '举报状态',
  `report_time` int(11) NOT NULL COMMENT '举报时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `web_report_del`
--
-- 创建时间： 2017-10-30 06:29:34
--

CREATE TABLE `web_report_del` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '举报id',
  `report_class` int(11) NOT NULL COMMENT '删除掉的举报类型',
  `report_video` int(11) NOT NULL COMMENT '举报作品名',
  `report_video_id` int(11) NOT NULL COMMENT '评论的id',
  `report_video_collect` int(11) NOT NULL COMMENT '作品下的集数',
  `report_text` varchar(255) NOT NULL COMMENT '举报内容',
  `report_tet` varchar(255) NOT NULL COMMENT '回复内容',
  `report_reason` varchar(255) NOT NULL COMMENT '举报理由',
  `report_static` int(11) NOT NULL DEFAULT '0' COMMENT '举报状态',
  `report_time` int(11) NOT NULL COMMENT '举报时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `web_site`
--
-- 创建时间： 2017-10-05 06:51:39
--

CREATE TABLE `web_site` (
  `id` int(11) NOT NULL,
  `bb` varchar(255) NOT NULL COMMENT '站点名称',
  `site` varchar(255) NOT NULL COMMENT '网站名称',
  `url` varchar(255) NOT NULL COMMENT '连接',
  `email` varchar(255) NOT NULL COMMENT '邮箱',
  `icp` varchar(255) NOT NULL COMMENT '备案',
  `statcode` varchar(255) NOT NULL COMMENT '第三方'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `web_site`
--

INSERT INTO `web_site` (`id`, `bb`, `site`, `url`, `email`, `icp`, `statcode`) VALUES
(1, '次元', '次元', 'www.ciyuan.com', 'liu937895433@gamil.com', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_front`
--
ALTER TABLE `admin_front`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_static`
--
ALTER TABLE `admin_static`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_user`
--
ALTER TABLE `admin_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`article_id`);

--
-- Indexes for table `article_column`
--
ALTER TABLE `article_column`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `article_type`
--
ALTER TABLE `article_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auth_group`
--
ALTER TABLE `auth_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auth_group_access`
--
ALTER TABLE `auth_group_access`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auth_power`
--
ALTER TABLE `auth_power`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `front_auth_group`
--
ALTER TABLE `front_auth_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `front_auth_group_access`
--
ALTER TABLE `front_auth_group_access`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `front_auth_power`
--
ALTER TABLE `front_auth_power`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `help_classify`
--
ALTER TABLE `help_classify`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `help_content`
--
ALTER TABLE `help_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `manage_guide`
--
ALTER TABLE `manage_guide`
  ADD PRIMARY KEY (`guide_id`);

--
-- Indexes for table `manage_guides`
--
ALTER TABLE `manage_guides`
  ADD PRIMARY KEY (`guides_id`);

--
-- Indexes for table `manage_push`
--
ALTER TABLE `manage_push`
  ADD PRIMARY KEY (`push_id`);

--
-- Indexes for table `manage_push_push`
--
ALTER TABLE `manage_push_push`
  ADD PRIMARY KEY (`push_id`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `member_del`
--
ALTER TABLE `member_del`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_integral`
--
ALTER TABLE `member_integral`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_level`
--
ALTER TABLE `member_level`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_sign`
--
ALTER TABLE `member_sign`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`news_id`);

--
-- Indexes for table `news_classify`
--
ALTER TABLE `news_classify`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news_comment`
--
ALTER TABLE `news_comment`
  ADD PRIMARY KEY (`comment_id`);

--
-- Indexes for table `news_comment_recovery`
--
ALTER TABLE `news_comment_recovery`
  ADD PRIMARY KEY (`comment_id`);

--
-- Indexes for table `news_feedback`
--
ALTER TABLE `news_feedback`
  ADD PRIMARY KEY (`view_id`);

--
-- Indexes for table `news_new`
--
ALTER TABLE `news_new`
  ADD PRIMARY KEY (`news_id`);

--
-- Indexes for table `news_view`
--
ALTER TABLE `news_view`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `view_id` (`view_id`);

--
-- Indexes for table `overall_domain`
--
ALTER TABLE `overall_domain`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `overall_email`
--
ALTER TABLE `overall_email`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `overall_optimize`
--
ALTER TABLE `overall_optimize`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `overall_register`
--
ALTER TABLE `overall_register`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `overall_search`
--
ALTER TABLE `overall_search`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `overall_seo`
--
ALTER TABLE `overall_seo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `overall_upload`
--
ALTER TABLE `overall_upload`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `overall_user`
--
ALTER TABLE `overall_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_category`
--
ALTER TABLE `system_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_front`
--
ALTER TABLE `system_front`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_money`
--
ALTER TABLE `system_money`
  ADD PRIMARY KEY (`money_id`);

--
-- Indexes for table `user_browse`
--
ALTER TABLE `user_browse`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_channel`
--
ALTER TABLE `user_channel`
  ADD PRIMARY KEY (`channel_id`),
  ADD UNIQUE KEY `id` (`channel_id`);

--
-- Indexes for table `user_coin`
--
ALTER TABLE `user_coin`
  ADD PRIMARY KEY (`coin_id`);

--
-- Indexes for table `user_collection`
--
ALTER TABLE `user_collection`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_detail`
--
ALTER TABLE `user_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_fans`
--
ALTER TABLE `user_fans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `video`
--
ALTER TABLE `video`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `video` ADD FULLTEXT KEY `text` (`video_text`);
ALTER TABLE `video` ADD FULLTEXT KEY `subject` (`title`);

--
-- Indexes for table `video_address`
--
ALTER TABLE `video_address`
  ADD PRIMARY KEY (`addressId`);

--
-- Indexes for table `video_collect`
--
ALTER TABLE `video_collect`
  ADD PRIMARY KEY (`collectionId`);

--
-- Indexes for table `video_data`
--
ALTER TABLE `video_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `video_del`
--
ALTER TABLE `video_del`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `video_nav`
--
ALTER TABLE `video_nav`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `video_sort`
--
ALTER TABLE `video_sort`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `web_report`
--
ALTER TABLE `web_report`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `web_report_del`
--
ALTER TABLE `web_report_del`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `web_site`
--
ALTER TABLE `web_site`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `admin_front`
--
ALTER TABLE `admin_front`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT COMMENT '自增id', AUTO_INCREMENT=3;
--
-- 使用表AUTO_INCREMENT `admin_static`
--
ALTER TABLE `admin_static`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT COMMENT 'id';
--
-- 使用表AUTO_INCREMENT `admin_user`
--
ALTER TABLE `admin_user`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT COMMENT '自增id', AUTO_INCREMENT=3;
--
-- 使用表AUTO_INCREMENT `article`
--
ALTER TABLE `article`
  MODIFY `article_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id';
--
-- 使用表AUTO_INCREMENT `article_column`
--
ALTER TABLE `article_column`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id';
--
-- 使用表AUTO_INCREMENT `article_type`
--
ALTER TABLE `article_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id';
--
-- 使用表AUTO_INCREMENT `auth_group`
--
ALTER TABLE `auth_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id';
--
-- 使用表AUTO_INCREMENT `auth_group_access`
--
ALTER TABLE `auth_group_access`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT COMMENT '自增id';
--
-- 使用表AUTO_INCREMENT `auth_power`
--
ALTER TABLE `auth_power`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id';
--
-- 使用表AUTO_INCREMENT `front_auth_group`
--
ALTER TABLE `front_auth_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id';
--
-- 使用表AUTO_INCREMENT `front_auth_group_access`
--
ALTER TABLE `front_auth_group_access`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT COMMENT '自增id';
--
-- 使用表AUTO_INCREMENT `front_auth_power`
--
ALTER TABLE `front_auth_power`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id';
--
-- 使用表AUTO_INCREMENT `help_classify`
--
ALTER TABLE `help_classify`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- 使用表AUTO_INCREMENT `help_content`
--
ALTER TABLE `help_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- 使用表AUTO_INCREMENT `manage_guide`
--
ALTER TABLE `manage_guide`
  MODIFY `guide_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- 使用表AUTO_INCREMENT `manage_guides`
--
ALTER TABLE `manage_guides`
  MODIFY `guides_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `manage_push`
--
ALTER TABLE `manage_push`
  MODIFY `push_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- 使用表AUTO_INCREMENT `manage_push_push`
--
ALTER TABLE `manage_push_push`
  MODIFY `push_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `member`
--
ALTER TABLE `member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id', AUTO_INCREMENT=25;
--
-- 使用表AUTO_INCREMENT `member_del`
--
ALTER TABLE `member_del`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id';
--
-- 使用表AUTO_INCREMENT `member_integral`
--
ALTER TABLE `member_integral`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id', AUTO_INCREMENT=2;
--
-- 使用表AUTO_INCREMENT `member_level`
--
ALTER TABLE `member_level`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id', AUTO_INCREMENT=12;
--
-- 使用表AUTO_INCREMENT `member_sign`
--
ALTER TABLE `member_sign`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `news`
--
ALTER TABLE `news`
  MODIFY `news_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `news_classify`
--
ALTER TABLE `news_classify`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- 使用表AUTO_INCREMENT `news_comment`
--
ALTER TABLE `news_comment`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `news_comment_recovery`
--
ALTER TABLE `news_comment_recovery`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `news_feedback`
--
ALTER TABLE `news_feedback`
  MODIFY `view_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id';
--
-- 使用表AUTO_INCREMENT `news_new`
--
ALTER TABLE `news_new`
  MODIFY `news_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `news_view`
--
ALTER TABLE `news_view`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `overall_domain`
--
ALTER TABLE `overall_domain`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `overall_email`
--
ALTER TABLE `overall_email`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- 使用表AUTO_INCREMENT `overall_optimize`
--
ALTER TABLE `overall_optimize`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- 使用表AUTO_INCREMENT `overall_register`
--
ALTER TABLE `overall_register`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- 使用表AUTO_INCREMENT `overall_search`
--
ALTER TABLE `overall_search`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `overall_seo`
--
ALTER TABLE `overall_seo`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT COMMENT 'id', AUTO_INCREMENT=2;
--
-- 使用表AUTO_INCREMENT `overall_upload`
--
ALTER TABLE `overall_upload`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- 使用表AUTO_INCREMENT `overall_user`
--
ALTER TABLE `overall_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `system_category`
--
ALTER TABLE `system_category`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT COMMENT '自增id', AUTO_INCREMENT=74;
--
-- 使用表AUTO_INCREMENT `system_front`
--
ALTER TABLE `system_front`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- 使用表AUTO_INCREMENT `system_money`
--
ALTER TABLE `system_money`
  MODIFY `money_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- 使用表AUTO_INCREMENT `user_browse`
--
ALTER TABLE `user_browse`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `user_channel`
--
ALTER TABLE `user_channel`
  MODIFY `channel_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `user_coin`
--
ALTER TABLE `user_coin`
  MODIFY `coin_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `user_collection`
--
ALTER TABLE `user_collection`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `user_detail`
--
ALTER TABLE `user_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `user_fans`
--
ALTER TABLE `user_fans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `video`
--
ALTER TABLE `video`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id', AUTO_INCREMENT=3;
--
-- 使用表AUTO_INCREMENT `video_address`
--
ALTER TABLE `video_address`
  MODIFY `addressId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- 使用表AUTO_INCREMENT `video_collect`
--
ALTER TABLE `video_collect`
  MODIFY `collectionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- 使用表AUTO_INCREMENT `video_data`
--
ALTER TABLE `video_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `video_del`
--
ALTER TABLE `video_del`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id';
--
-- 使用表AUTO_INCREMENT `video_nav`
--
ALTER TABLE `video_nav`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id', AUTO_INCREMENT=54;
--
-- 使用表AUTO_INCREMENT `video_sort`
--
ALTER TABLE `video_sort`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id';
--
-- 使用表AUTO_INCREMENT `web_report`
--
ALTER TABLE `web_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `web_report_del`
--
ALTER TABLE `web_report_del`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `web_site`
--
ALTER TABLE `web_site`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
