-- 帖子表
create table `board`(
    `id` int(11) not null primary key auto_increment comment '主贴id',
    `subject` varchar(100) not null comment '标题',
    `author` varchar(20) not null comment '发帖人',
    `idate` datetime not null comment '发帖时间',
    `replies` int(11) not null default '0' comment '回帖数量',
    `body` text comment '主贴内容',
    `ndate` datetime comment '最新回复时间',
    `ip` varchar(15)
);