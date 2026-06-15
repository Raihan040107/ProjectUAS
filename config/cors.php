'allowed_origins' => [
'https://finsustain.vercel.app/',
], // mungkin ada yang salah di baris lain

'allowed_origins' => ['https://finsustain-web.vercel.app'],
'allowed_origins_patterns' => [], // ← ini harus array kosong, bukan angka
'allowed_headers' => ['*'], // ← ini juga harus array
'exposed_headers' => [], // ← ini juga
'allowed_methods' => ['*'], // ← ini juga