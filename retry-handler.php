<?php

class RetryHandler {

    public static function retry(callable $fn, $maxAttempt = 3) {

        $attempt = 0;

        while ($attempt < $maxAttempt) {

            try {
                return $fn();

            } catch (Exception $e) {

                $attempt++;

                if ($attempt >= $maxAttempt) {
                    throw $e;
                }

                sleep(2); // backoff
            }
        }
    }
}
