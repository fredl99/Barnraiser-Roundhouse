-------------------------------------------------------------------------
-- This file is part of Roundhouse
-- 
-- Copyright (C) 2003-2008 Barnraiser
-- http:--www.barnraiser.org/
-- info@barnraiser.org
-- 
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
-- 
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
-- 
-- You should have received a copy of the GNU General Public License
-- along with this program; see the file COPYING.txt.  If not, see
-- <http:--www.gnu.org/licenses/>
-------------------------------------------------------------------------


-- Table structure for table `roundhouse_blog`
CREATE TABLE IF NOT EXISTS `roundhouse_blog` (
  `blog_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `blog_title` varchar(100) NOT NULL,
  `blog_title_display` varchar(255) NOT NULL,
  `blog_body` text NOT NULL,
  `blog_create_datetime` datetime NOT NULL,
  `blog_import_body` text,
  `blog_import_link` varchar(255) default NULL,
  `blog_import_title` varchar(255) default NULL,
  `blog_import_source_title` varchar(255) default NULL,
  `blog_import_source_link` varchar(255) default NULL,
  `blog_highlight` int(1) NOT NULL,
  `blog_accept_comment` int(1) default NULL,
  `blog_published` int(1) NOT NULL default '0',
  PRIMARY KEY  (`blog_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- Table structure for table `roundhouse_blog_comment`
CREATE TABLE IF NOT EXISTS `roundhouse_blog_comment` (
  `comment_id` int(11) NOT NULL auto_increment,
  `blog_id` int(11) NOT NULL,
  `comment_user_name` varchar(100) NOT NULL,
  `comment_body` text NOT NULL,
  `comment_email` varchar(255) default NULL,
  `comment_create_datetime` datetime NOT NULL,
  PRIMARY KEY  (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table structure for table `roundhouse_tag`
CREATE TABLE IF NOT EXISTS `roundhouse_tag` (
  `user_id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `tag_name` varchar(255) NOT NULL,
  `tag_display_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table structure for table `roundhouse_user`
CREATE TABLE IF NOT EXISTS `roundhouse_user` (
  `user_id` int(11) NOT NULL auto_increment,
  `user_webspace` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_openid` varchar(255) default NULL,
  `user_live` int(1) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_location` varchar(100) NOT NULL,
  `user_dob` date NOT NULL,
  `user_create_datetime` datetime NOT NULL,
  `user_registration_key` varchar(100) default NULL,
  `user_last_login_datetime` datetime NOT NULL,
  `user_blog_title` varchar(100) default NULL,
  `user_blog_description` text,
  `user_blog_theme` varchar(100) default NULL,
  `user_blog_language` varchar(3) default NULL,
  `user_email_notify` int(1) default NULL,
  `openid_server` varchar(255) default NULL,
  `openid_delegate` varchar(255) default NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- ENDS