<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class YousignService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;
    private string $apiUrl;

    public function __construct(HttpClientInterface $httpClient, ParameterBagInterface $params)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $_ENV['YOUSIGN_API_KEY'];
        $this->apiUrl = $_ENV['YOUSIGN_API_URL'];
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * 1. Créer une demande de signature
     */
    public function createSignatureRequest(string $name = "Signature Request"): string
    {
        $response = $this->httpClient->request('POST', $this->getApiUrl() . '/signature_requests', [
            'headers' => $this->getHeaders(),
            'json' => [
                'name' => $name,
                'delivery_mode' => 'email',
                'timezone' => 'Europe/Paris',
            ],
        ]);

        $data = $response->toArray();
        return $data['id']; // Retourne l'ID de la demande de signature
    }

    /**
     * 2. Ajouter un document à la demande de signature
     */
    public function addDocumentToSignatureRequest(string $signatureRequestId, string $path): string
    {
        $absolutePath = realpath($path); // Vérifie le chemin absolu
        if ($absolutePath === false) {
            dd('Le fichier n\'existe pas à cet emplacement : ' . $path);
        }
        $curl = curl_init();
        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL => ($this->getApiUrl() . "/signature_requests/{$signatureRequestId}/documents"),
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => [
                    'file' => new \CURLFile($path, 'application/pdf'),
                    'nature' => 'signable_document',
                    'parse_anchors' => 'true'
                ],
                CURLOPT_HTTPHEADER => [
                    sprintf('Authorization: Bearer %s', $this->getApiKey()),
                ],
            ]);
        
        $response = curl_exec($curl);
        //dd($documentUploadResponse);

        /*$response = $this->httpClient->request('POST', $this->getApiUrl() . "/signature_requests/{$signatureRequestId}/documents", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getApiKey(),
                'Content-Type' => 'multipart/form-data'
            ],
            'body' => [
                'file' => new \CURLFile($path, 'application/pdf'),
                'nature' => 'signable_document',
                'parse_anchors' => 'true',
            ],
        ]);
        dd($response->getContent());*/

        //dd($response);
        $data = json_decode($response, true);
        return $data['id']; // Retourne l'ID du document ajouté
    }

    public function addDocumentToSignatureRequest_(string $signatureRequestId, string $path): string
    {
        $absolutePath = realpath($path);
        if ($absolutePath === false) {
            throw new \RuntimeException("Le fichier n'existe pas : " . $path);
        }

        $response = $this->httpClient->request('POST', $this->getApiUrl() . "/signature_requests/{$signatureRequestId}/documents", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getApiKey(),
            ],
            'extra' => [
                'headers' => ['Content-Type' => 'multipart/form-data'],
            ],
            'body' => [
                'file' => fopen($absolutePath, 'r'), // Ouvre le fichier pour la lecture
                'nature' => 'signable_document',
                'parse_anchors' => 'true',
            ],
        ]);

        // Vérification de la réponse
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200 && $statusCode !== 201) {
            throw new \RuntimeException("Erreur API Yousign: " . $response->getContent(false));
        }

        $data = $response->toArray();
        return $data['id'] ?? throw new \RuntimeException("L'API n'a pas retourné d'ID.");
    }

    /**
     * 3. Ajouter un signataire à la demande de signature
     */
    public function addSignerToSignatureRequest(string $signatureRequestId, string $documentId, array $signerInfo, array $fields)
    {
        foreach ($fields as &$item) {  
            $item["document_id"] = $documentId;
        }
        unset($item);

        $data = [
            "info" => [
                "first_name" => $signerInfo['first_name'],
                "last_name" => $signerInfo['last_name'],
                "email" => $signerInfo['email'],
                "phone_number" => $signerInfo['phone_number'],
                "locale" => "fr"
            ],
            "signature_authentication_mode" => "no_otp",
            "signature_level" => "electronic_signature",
            "fields" => $fields
        ];
        //dd(json_encode($data));
        $this->httpClient->request('POST', $this->getApiUrl() . "/signature_requests/{$signatureRequestId}/signers", [
            'headers' => $this->getHeaders(),
            'json' => $data,
        ]);
    }

    /**
     * 4. Activer la demande de signature
     */
    public function activateSignatureRequest(string $signatureRequestId)
    {
        $this->httpClient->request('POST', $this->getApiUrl() . "/signature_requests/{$signatureRequestId}/activate", [
            'headers' => $this->getHeaders(),
        ]);
    }

    /**
     * Récupère les headers d'authentification
     */
    private function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->getApiKey(),
            'Content-Type' => 'application/json'
        ];
    }

    public function Sign($client, $contractList, $fields):string{
        $path = './public/uploads/contracts/compiled/';
        $finalFileName = $client->getFirstName()."_".$client->getLastName()."_".date('U').rand(100000,999999).".pdf";

        $concat = new ConcatService();
		$concat->setFiles($contractList);
		$concat->concat();
        $concat->Output($path.$finalFileName, 'F');

                //dd($fields);
        $signerInfo = [
            'first_name' => $client->getFirstName(),
            'last_name' => $client->getLastName(),
            'email' => $client->getEmail(),
            'phone_number' => $client->getIndicative() . substr($client->getPhone(), 1),
        ];

                // 1. Créer une demande de signature
        $signatureRequestId = $this->createSignatureRequest();
        $documentId = $this->addDocumentToSignatureRequest_($signatureRequestId, $path.$finalFileName);
        $this->addSignerToSignatureRequest($signatureRequestId, $documentId, $signerInfo, $fields);
        $this->activateSignatureRequest($signatureRequestId);

        return $signatureRequestId;
    }
}
