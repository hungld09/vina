<?php
if(!defined("secret_key")) define("secret_key", "s3cr3t_key"); //key serect cho sessionkey
if(!defined("SESSION_EXPIRY")) define("SESSION_EXPIRY", 15*60*60); // thoi gian expiry cho sessionkey

if(!defined("CHANNEL_TYPE_WEB")) define("CHANNEL_TYPE_WEB", "WEB");
if(!defined("CHANNEL_TYPE_WAP")) define("CHANNEL_TYPE_WAP", "WAP");
if(!defined("CHANNEL_TYPE_SMS")) define("CHANNEL_TYPE_SMS", "SMS");
if(!defined("CHANNEL_TYPE_APP")) define("CHANNEL_TYPE_APP", "CLIENT");
if(!defined("CHANNEL_TYPE_UNSUB")) define("CHANNEL_TYPE_UNSUB", "UNSUB");
if(!defined("CHANNEL_TYPE_CSKH")) define("CHANNEL_TYPE_CSKH", "CSKH");
if(!defined("CHANNEL_TYPE_ADMIN")) define("CHANNEL_TYPE_ADMIN", "ADMIN");
if(!defined("CHANNEL_TYPE_MAXRETRY")) define("CHANNEL_TYPE_MAXRETRY", "MAXRETRY");
if(!defined("CHANNEL_TYPE_SUBNOTEXIST")) define("CHANNEL_TYPE_SUBNOTEXIST", "SUBNOTEXT");
if(!defined("CHANNEL_TYPE_SYSTEM")) define("CHANNEL_TYPE_SYSTEM", "SYSTEM");


if(!defined("PARAM_INVALID")) define("PARAM_INVALID", "1"); 
if(!defined("SUB_NOT_EXIST")) define("SUB_NOT_EXIST", "2"); 
if(!defined("SESSION_KEY_INVALID")) define("SESSION_KEY_INVALID", "3"); 

// Error register service
if(!defined("SUCCEED")) define("SUCCEED", "0"); 
if(!defined("FAIL_MONEY")) define("FAIL_MONEY", "1"); 
if(!defined("FAIL_SERVER")) define("FAIL_SERVER", "2");

if(!defined("LEVEL_NOT_EXIT")) define("LEVEL_NOT_EXIT", "5");

// hocde
if(!defined("VT")) define("VT", "VT");
if(!defined("MOBI")) define("MOBI", "MOBI");
if(!defined("VINA")) define("VINA", "VINA");
if(!defined("VCOIN")) define("VCOIN", "VCOIN");
if(!defined("GATE")) define("GATE", "GATE");
if(!defined("GM")) define("GM", "GM");
if(!defined("HOCDE")) define("HOCDE", "HOCDE"); 
if(!defined("ONCASH")) define("ONCASH", "ONCASH");

// net2e
if(!defined("VIETTELCARD")) define("VIETTELCARD", "VIETTELCARD");
if(!defined("MOBICARD")) define("MOBICARD", "MOBICARD");
if(!defined("VINACARD")) define("VINACARD", "VINACARD");
if(!defined("ONCASH")) define("ONCASH", "ONCASH");
if(!defined("HOCDE")) define("HOCDE", "HOCDE");

if(!defined("KEY")) define("KEY", "SOCOTECTJSC");

if(!defined("PURCHASE_TYPE_NEW")) define("PURCHASE_TYPE_NEW", "1");
if(!defined("PURCHASE_TYPE_QUESTION")) define("PURCHASE_TYPE_QUESTION", "5");

if(!defined("IPSERVER")) define("IPSERVER", "http://123.30.200.85/");
if(!defined("EMAIL")) define("EMAIL", "cskh.socotec02@gmail.com");
//sms
if(!defined("USER_NOT_OK")) define("USER_NOT_OK", "01"); //Ma the nap thanh cong
if(!defined("USER_NOT_EXIST")) define("USER_NOT_EXIST", "02"); //Ma the nap thanh cong
if(!defined("ORDERID_EXIST")) define("ORDERID_EXIST", "04"); //Ma the nap thanh cong
if(!defined("MOBILE_EXIST")) define("MOBILE_EXIST", "03"); //Ma the nap thanh cong
if(!defined("SIGNATURE_EXIT")) define("SIGNATURE_EXIT", "05"); //Ma the nap thanh cong











