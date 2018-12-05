<?php namespace OFFLINE\Clockwork\Classes\DataSource;

use Clockwork\DataSource\DataSource;
use Clockwork\Request\Request;
use Clockwork\Request\Timeline;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Event;
use October\Rain\Events\Dispatcher;


/**
 * Data source for October Mailer, provides mail log
 */
class OctoberMailerDataSource extends DataSource
{
    /**
     * Timeline data structure
     */
    protected $timeline;
    /**
     * @var Event
     */
    protected $event;

    /**
     * Create a new data source, takes Swift_Mailer instance as an argument
     */
    public function __construct(Dispatcher $events)
    {
        $this->timeline = new Timeline();
    }

    /**
     * Invoked immediately before the Message is sent.
     */
    public function beforeSendPerformed(Message $message)
    {
        $headers = [];
        foreach ($message->getHeaders()->getAll() as $header) {
            $headers[$header->getFieldName()] = $header->getFieldBody();
        }

        $this->timeline->startEvent(
            'email ' . $message->getId(),
            'Sending an email message',
            null,
            [
                'from'    => $this->addressToString($message->getFrom()),
                'to'      => $this->addressToString($message->getTo()),
                'subject' => $message->getSubject(),
                'headers' => $headers,
            ]
        );
    }

    /**
     * Invoked immediately after the Message is sent.
     */
    public function sendPerformed(Message $message)
    {
        $this->timeline->endEvent('email ' . $message->getId());
    }

    protected function addressToString($address)
    {
        if ( ! $address) {
            return;
        }

        foreach ($address as $email => $name) {
            if ($name) {
                $address[$email] = "$name <$email>";
            } else {
                $address[$email] = $email;
            }
        }

        return implode(', ', $address);
    }

    /**
     * Adds email data to the request
     */
    public function resolve(Request $request)
    {
        $request->emailsData = array_merge($request->emailsData, $this->timeline->finalize());

        return $request;
    }
}
