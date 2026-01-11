<?php

namespace App\Services;

class TelegramCctvAlertParser
{
    /**
     * Parse CCTV offline alert message
     * 
     * @param string $messageText
     * @return array|null Returns parsed data or null if not a CCTV alert
     */
    public static function parse(string $messageText): ?array
    {
        // Check if this is a CCTV offline alert
        if (!preg_match('/📡\s*ALERT\s+CCTV\s+OFFLINE/i', $messageText)) {
            return null;
        }

        $data = [
            'type' => 'cctv_offline_alert',
            'site' => null,
            'alert_date' => null,
            'offline_count' => 0,
            'online_count' => 0,
            'units' => [],
        ];

        // Parse Site
        if (preg_match('/Site:\s*([^\n]+)/i', $messageText, $matches)) {
            $data['site'] = trim($matches[1]);
        }

        // Parse Tanggal
        if (preg_match('/Tanggal:\s*([^\n]+)/i', $messageText, $matches)) {
            $dateString = trim($matches[1]);
            try {
                $data['alert_date'] = \Carbon\Carbon::parse($dateString)->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                $data['alert_date'] = $dateString; // Keep original if parsing fails
            }
        }

        // Parse Jumlah Offline
        if (preg_match('/Jumlah\s+Offline:\s*(\d+)/i', $messageText, $matches)) {
            $data['offline_count'] = (int) $matches[1];
        }

        // Parse Jumlah Online
        if (preg_match('/Jumlah\s+Online:\s*(\d+)/i', $messageText, $matches)) {
            $data['online_count'] = (int) $matches[1];
        }

        // Parse units (lines starting with 🔴)
        $lines = explode("\n", $messageText);
        $inUnitsSection = false;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Check if we're in the units section
            if (preg_match('/UNIT\s*\|\|\s*LAST_CONNECT/i', $line)) {
                $inUnitsSection = true;
                continue;
            }

            // Skip separator lines
            if (preg_match('/^[-=]+$/', $line)) {
                continue;
            }

            // Parse unit line: 🔴 BMO2-PM-0022 | PT - C2_FRONT_UTARA_PTZ | 2026-01-09 10:33:03
            if ($inUnitsSection && preg_match('/🔴\s*([^\|]+)\s*\|\s*([^\|]+)\s*\|\s*(.+)/', $line, $matches)) {
                $unitCode = trim($matches[1]);
                $unitName = trim($matches[2]);
                $lastConnect = trim($matches[3]);
                
                // Parse last connect date
                $lastConnectDate = null;
                if ($lastConnect !== '-' && !empty($lastConnect)) {
                    try {
                        $lastConnectDate = \Carbon\Carbon::parse($lastConnect)->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                        $lastConnectDate = $lastConnect; // Keep original if parsing fails
                    }
                }

                $data['units'][] = [
                    'unit_code' => $unitCode,
                    'unit_name' => $unitName,
                    'last_connect' => $lastConnectDate ?? $lastConnect,
                ];
            }
        }

        return $data;
    }

    /**
     * Check if message is a CCTV offline alert
     */
    public static function isCctvAlert(string $messageText): bool
    {
        return preg_match('/📡\s*ALERT\s+CCTV\s+OFFLINE/i', $messageText) === 1;
    }
}

