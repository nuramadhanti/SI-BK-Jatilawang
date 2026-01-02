# Dokumentasi Rumus Perhitungan Skor Akhir Permohonan Konseling

## Rumus Umum

```
Skor Akhir = (k1 × bobot) + (k2 × bobot) + (k3 × bobot) + (k4 × bobot) + ...
```

Dimana:
- **k1, k2, k3, k4, ...** = Skor sub-kriteria yang dipilih oleh user
- **bobot** = Bobot masing-masing kriteria (nilai 0-1, total semua bobot = 1)

## Komponen Kriteria

Saat ini sistem memiliki 4 komponen kriteria utama:

1. **Tingkat Urgensi** (bobot: 0.25)
   - Tidak Mendesak: 20
   - Cukup Mendesak: 40
   - Mendesak: 70
   - Sangat Mendesak/Kritikal: 90

2. **Dampak Masalah** (bobot: 0.25)
   - Dampak Ringan: 20
   - Dampak Sedang: 40
   - Dampak Berat: 70
   - Dampak Sangat Berat: 90

3. **Kategori Masalah** (bobot: 0.25)
   - Masalah Pribadi: 20
   - Masalah Akademik: 40
   - Masalah Sosial: 70
   - Masalah Kesehatan Mental: 90

4. **Riwayat Konseling** (bobot: 0.25)
   - Pertama Kali: 20
   - Pernah Konseling 1x: 40
   - Pernah Konseling 2-3x: 70
   - Pernah Konseling >3x: 90

## Contoh Perhitungan

### Skenario 1: Kasus Prioritas Tinggi
```
- Tingkat Urgensi: Sangat Mendesak (90) × 0.25 = 22.5
- Dampak Masalah: Dampak Sangat Berat (90) × 0.25 = 22.5
- Kategori Masalah: Masalah Kesehatan Mental (90) × 0.25 = 22.5
- Riwayat Konseling: Pertama Kali (20) × 0.25 = 5.0

TOTAL SKOR AKHIR = 22.5 + 22.5 + 22.5 + 5.0 = 72.5
```

### Skenario 2: Kasus Prioritas Menengah
```
- Tingkat Urgensi: Mendesak (70) × 0.25 = 17.5
- Dampak Masalah: Dampak Berat (70) × 0.25 = 17.5
- Kategori Masalah: Masalah Sosial (70) × 0.25 = 17.5
- Riwayat Konseling: Pernah Konseling 1x (40) × 0.25 = 10.0

TOTAL SKOR AKHIR = 17.5 + 17.5 + 17.5 + 10.0 = 62.5
```

### Skenario 3: Kasus Prioritas Rendah
```
- Tingkat Urgensi: Tidak Mendesak (20) × 0.25 = 5.0
- Dampak Masalah: Dampak Ringan (20) × 0.25 = 5.0
- Kategori Masalah: Masalah Pribadi (20) × 0.25 = 5.0
- Riwayat Konseling: Pernah Konseling >3x (90) × 0.25 = 22.5

TOTAL SKOR AKHIR = 5.0 + 5.0 + 5.0 + 22.5 = 37.5
```

## Pengelompokan Prioritas

| Range Skor | Klasifikasi | Rekomendasi |
|------------|------------|------------|
| 0 - 32.5  | Rendah     | Tindak lanjut normal |
| 32.5 - 65 | Sedang     | Perhatian khusus |
| 65 - 85   | Tinggi     | Prioritas |
| 85 - 100  | Sangat Tinggi | Prioritas Urgent |

## Implementasi di Kode

### Model: PermohonanKonseling

Terdapat beberapa method untuk perhitungan dan display:

1. **hitungSkorAkhir($kriteriaData)** - Static method
   ```php
   // Digunakan di controller saat create permohonan
   $skorAkhir = PermohonanKonseling::hitungSkorAkhir($kriteriaData);
   ```

2. **hitungSkorPrioritas()** - Instance method
   ```php
   // Menghitung ulang skor dari relasi permohonanKriterias
   $skor = $permohonan->hitungSkorPrioritas();
   ```

3. **getBreakdownSkor()** - Lihat detail per kriteria
   ```php
   $breakdown = $permohonan->getBreakdownSkor();
   // Mengembalikan array dengan detail setiap kriteria
   ```

4. **getRumusSkorAkhir()** - Rumus dalam format readable
   ```php
   $rumus = $permohonan->getRumusSkorAkhir();
   // Output: "(90 × 0.25) + (70 × 0.25) + ... = 55"
   ```

5. **getBreakdownSkorHtml()** - HTML table untuk display
   ```php
   echo $permohonan->getBreakdownSkorHtml();
   // Menampilkan tabel breakdown skor
   ```

### Controller: PermohonanKonselingController

Di method `store()`, perhitungan dilakukan dengan:
```php
foreach ($kriteriaSubmitted as $subKriteriaId) {
    $subKriteria = SubKriteria::with('kriteria')->findOrFail($subKriteriaId);
    $skor = $subKriteria->skor;
    $bobot = $subKriteria->kriteria->bobot;
    
    // Skor Akhir = (skor × bobot)
    $skorAkhir += ($skor * $bobot);
}
$skorAkhir = round($skorAkhir, 2);
```

## Modifikasi Rumus di Masa Depan

Jika ada perubahan jumlah kriteria atau bobot:

1. **Update kriteria di database**
   - Ubah data di tabel `kriterias` dan `sub_kriterias`
   - Admin dapat mengelola via `/kriteria` panel

2. **Method hitungSkorAkhir() akan otomatis adapt**
   - Tidak perlu ubah kode, karena iterasi dinamis

3. **Pengujian**
   - Test dengan berbagai kombinasi sub-kriteria
   - Verifikasi skor akhir match dengan rumus manual

## API untuk Ambil Breakdown

Endpoint: `GET /api/permohonan-konseling/{id}/breakdown` (future implementation)

Response:
```json
{
  "skor_akhir": 55,
  "breakdown": [
    {
      "kriteria": "Tingkat Urgensi",
      "skor": 90,
      "bobot": 0.25,
      "hasil": 22.5
    }
  ],
  "rumus": "(90 × 0.25) + (70 × 0.25) + ... = 55"
}
```
