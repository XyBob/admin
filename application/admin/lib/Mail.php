<?php
namespace app\admin\lib;

use Nette\Mail\Message;

class Mail extends Message
{
    public $config;
    protected $from;
    protected $to;
    protected $title;
    protected $body;

    function __construct($to)
    {
        $this->config = config('mail');
        $this->setFrom($this->config['username']);
        if (is_array($to)) {
            foreach ($to as $email) {
                $this->addTo($email);
            }
        } else {
            $this->addTo($to);
        }
    }

    public function from($from = null)
    {
        if (!$from) {
            throw new \InvalidArgumentException("邮件发送地址不能为空！");
        }
        $this->setFrom($from);
        return $this;
    }

    public static function to($to = null)
    {
        if (!$to) {
            throw new \InvalidArgumentException("邮件接收地址不能为空！");
        }
        return new Mail($to);
    }

    public function title($title = null)
    {
        if (!$title) {
            throw new \InvalidArgumentException("邮件标题不能为空！");
        }
        $this->setSubject($title);
        return $this;
    }

    public function content($content = null)
    {
        if (!$content) {
            throw new \InvalidArgumentException("邮件内容不能为空！");
        }
        $this->setHTMLBody($content);
        return $this;
    }

    public function send()
    {
    	$mailer = new \Nette\Mail\SmtpMailer($this->config);
    	$mailer->send($this);
    }
}