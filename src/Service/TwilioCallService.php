<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Twilio\Rest\Client;

class TwilioCallService
{
    private Client $twilio;
    private EntityManagerInterface $em;
    private string $twilioNumber;

    public function __construct(EntityManagerInterface $em, string $twilioSid, string $twilioToken, string $twilioNumber)
    {
        $this->em = $em;
        $this->twilio = new Client($twilioSid, $twilioToken);
        $this->twilioNumber = $twilioNumber;
    }

    public function callClient(string $clientNumber): void
    {
        $call = $this->twilio->calls->create(
            $clientNumber,
            $this->twilioNumber,
            [
                "machineDetection" => "Enable",
                "machineDetectionTimeout" => 5,
                "statusCallback" => "https://yourdomain.com/twilio/callback",
                "statusCallbackEvent" => ["answered", "completed"],
                "url" => "https://yourdomain.com/twiml-response.xml"
            ]
        );
    }

    public function handleCallStatus(array $data): void
    {
        $callSid = $data['CallSid'] ?? null;
        $answeredBy = $data['AnsweredBy'] ?? null;
        $callStatus = $data['CallStatus'] ?? null;

        if ($answeredBy === 'human' && $callSid) {
            $agent = $this->findAvailableAgent();
            if ($agent) {
                $agent->setCurrentCallSid($callSid);
                $agent->setIsAvailable(false);
                $this->em->persist($agent);
                $this->em->flush();
            }
        }

        if ($callStatus === 'completed' && $callSid) {
            $agent = $this->em->getRepository(User::class)->findOneBy(['currentCallSid' => $callSid]);
            if ($agent) {
                $agent->setIsAvailable(true);
                $agent->setCurrentCallSid(null);
                $this->em->persist($agent);
                $this->em->flush();
            }
        }
    }

    private function findAvailableAgent(): ?User
    {
        $agents = $this->em->getRepository(User::class)->findBy(['role' => 'ROLE_AGENT', 'isAvailable' => true]);
        return $agents ? $agents[array_rand($agents)] : null;
    }
}
