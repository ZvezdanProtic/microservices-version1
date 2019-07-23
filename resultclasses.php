<?php

class LoginUserResultClass
{
  public $authtoken = "";
  public $error = false;
  public $errormessage = "";
}

class LogoffUserResultClass
{
  public $error = false;
  public $errormessage = "";
}

class RequestCallResultClass
{
  public $error = true;
  public $callid = "";
  public $errormessage = "";
}

class CancelCallResultClass
{
  public $error = true;
  public $errormessage = "";
}

class AcceptCallResultClass
{
  public $error = true;
  public $errormessage = "";
  public $callid = "";
}

class PerformCallResultClass
{
  public $error = true;
  public $errormessage = "";
}

class GetCallLinkResultClass
{
  public $error = true;
  public $errormessage = "";
  public $linkaddress = "";
  public $roomid = "";
}

class FinishCallResultClass
{
  public $error = true;
  public $errormessage = "";
}

class EndCallResultClass
{
  public $error = true;
  public $errormessage = "";
}

class CallFinishedResultClass
{
  public $error = true;
  public $callfinished = true;
  public $errormessage = "";
}

?>