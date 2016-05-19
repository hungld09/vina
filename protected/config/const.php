<?php
if(!defined("secret_key")) define("secret_key", "s3cr3t_key"); //key serect cho sessionkey
if(!defined("SESSION_EXPIRY")) define("SESSION_EXPIRY", 15*60*60); // thoi gian expiry cho sessionkey

if(!defined("CPS_OK")) define("CPS_OK", "0"); //Lenh thuc hien thanh cong
if(!defined("CPS_OK_11")) define("CPS_OK_11", "11"); //VNP da ghi nhan roi
if(!defined("CHARGING_ERROR_CODE_NONE")) define("CHARGING_ERROR_CODE_NONE", 0); //Lenh thuc hien thanh cong
if(!defined("NOK_NO_MORE_CREDIT_AVAILABLE")) define("NOK_NO_MORE_CREDIT_AVAILABLE", 1); //Prepaid only
if(!defined("CHARGING_ERROR_AUTH_FAIL")) define("CHARGING_ERROR_AUTH_FAIL", 2); //wrong user or password
if(!defined("CHARGING_ERROR_CHARGING_NOT_COMPLETE")) define("CHARGING_ERROR_CHARGING_NOT_COMPLETE", 3); //Timeout or IN Billing internal error
if(!defined("CHARGING_ERROR_OTHER")) define("CHARGING_ERROR_OTHER", 4); 
if(!defined("CHARGING_ERROR_WRONG_PHONE")) define("CHARGING_ERROR_WRONG_PHONE", 5); //Wrong subscriber number
if(!defined("CHARGING_ERROR_SUB_NOT_EXIST")) define("CHARGING_ERROR_SUB_NOT_EXIST", 6); //Subs Does Not Exist in DB
if(!defined("CHARGING_ERROR_OVER_CHARGE_LIMIT_DAY")) define("CHARGING_ERROR_OVER_CHARGE_LIMIT_DAY", 7); //Postpaid only: daily
if(!defined("CHARGING_ERROR_OVER_CHARGE_LIMIT_MONTH")) define("CHARGING_ERROR_OVER_CHARGE_LIMIT_MONTH", 17); //Postpaid only: monthly
if(!defined("CHARGING_ERROR_INTERNAL_ERROR")) define("CHARGING_ERROR_INTERNAL_ERROR", 8);
if(!defined("CHARGING_ERROR_CONFIG_ERROR")) define("CHARGING_ERROR_CONFIG_ERROR", 9);
if(!defined("CHARGING_ERROR_REQUESTID_NULL")) define("CHARGING_ERROR_REQUESTID_NULL", 10);
if(!defined("CHARGING_ERROR_UNKNOW_IP")) define("CHARGING_ERROR_UNKNOW_IP", 99);
if(!defined("CHARGING_ERROR_SYNCTAX_XML")) define("CHARGING_ERROR_SYNCTAX_XML", 100);

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

if(!defined("USING_TYPE_REGISTER")) define("USING_TYPE_CANCEL", 0);
if(!defined("USING_TYPE_REGISTER")) define("USING_TYPE_REGISTER", 1);
if(!defined("USING_TYPE_WATCH")) define("USING_TYPE_WATCH", 2);
if(!defined("USING_TYPE_DOWNLOAD")) define("USING_TYPE_DOWNLOAD", 3);
if(!defined("USING_TYPE_SEND_GIFT")) define("USING_TYPE_SEND_GIFT", 4);
if(!defined("USING_TYPE_RECEIVE_GIFT")) define("USING_TYPE_RECEIVE_GIFT", 5);
if(!defined("USING_TYPE_EXTEND_TIME")) define("USING_TYPE_EXTEND_TIME", 6); //gia han thgian xem mien phi 3G
if(!defined("USING_TYPE_CHARGING_SMS")) define("USING_TYPE_CHARGING_SMS", 7); //thu phi 100d cho sms
if(!defined("SERVICE_PHONE_NUMBER")) define("SERVICE_PHONE_NUMBER", 9033); //thu phi 100d cho sms

//Vasprovisoning
if(!defined("CPS_OK_3")) define("CPS_OK_3", "3"); //Lenh thuc hien thanh cong va khong bi tru cuoc
if(!defined("CPS_OK_4")) define("CPS_OK_4", "4"); //Lenh thuc hien thanh cong va bi tru cuoc
if(!defined("CPS_OK_6")) define("CPS_OK_6", "6"); //Nang cap goi thanh cong khong ro bi tru tien
if(!defined("CPS_OK_7")) define("CPS_OK_7", "7"); //Nang cap goi thanh cong va khong bi tru tien
if(!defined("CPS_OK_8")) define("CPS_OK_8", "8"); //Nang cap goi thanh cong va bi tru tien

define("PURCHASE_TYPE_NEW", 1);
define("PURCHASE_TYPE_RECUR", 2);
define("PURCHASE_TYPE_CANCEL", 3);
define("PURCHASE_TYPE_FORCE_CANCEL", 4);
define("PURCHASE_TYPE_EXTEND_TIME", 5);

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











