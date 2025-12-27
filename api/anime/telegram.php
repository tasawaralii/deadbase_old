<?php

class Telegram {

    private $botToken = '7071121072:AAHiGKQEf2AmGyUStg9B_qAzPIymTfy8TZY';
    private $babybossChatId = '5962388220';

    public function sendError($error) {
        $message = "URl: " ."https://api.deadbase.xyz" .$_SERVER['REQUEST_URI'] . "\n\n".
                    "Error: ".$error;
        return $this->send($message);
    }

    private function send($message) {

        $telegramApiUrl = 'https://api.telegram.org/bot' . $this->botToken . '/sendMessage';

        $postFields = json_encode([
            'chat_id' => $this->babybossChatId,
            'text' => $message
        ]);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_URL, $telegramApiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $result = ['status' => 'error', 'message' => curl_error($ch)];
        } else {
            $result = ['status' => 'success', 'message' => 'Sent successfully'];
        }

        curl_close($ch);
        return $result;
    }
}
