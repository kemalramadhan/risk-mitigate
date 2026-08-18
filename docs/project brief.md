**Role & Objective** Bertindaklah sebagai Senior Full-Stack Developer dan System Architect. Saya sedang mengerjakan skripsi untuk membangun Sistem Pendukung Keputusan (SPK) bernama **Risk Mitigate** untuk Mitigasi Risiko Proyek TI dan R&D.

**Tech Stack**

*   Backend & Routing: Laravel (PHP)
    
*   Frontend: Laravel Blade, Bootstrap / Tailwind CSS (UI dengan nuansa Enterprise/IT GRC)
    
*   Data Processing & Algorithm: Python (terintegrasi dengan Laravel untuk memproses AHP).
    
*   Database: MySQL
    

#### Detail sistem:

**Database & Knowledge Base:**Hasil ekstraksi FP-Growth telah saya siapkan dalam file Excel "Knowledge Base" (berisi kolom: Domain, Faktor\_Risiko, Risk\_Level, Strategi\_Mitigasi). Data ini harus di-_seed_ ke dalam tabel MySQL. Setiap kombinasi Domain dan Risk\_Level memiliki tepat 9 Faktor\_Risiko.

**Fitur Registrasi & Autentikasi:**User diminta untuk login atau sign up terlebih dahulu. Minta user untuk menggunakan email unik yang belum digunakan sebelumnya.

**Fitur 1: Pendaftaran Proyek & Triage (Asesmen Awal)**

1.  Saat User mendaftarkan proyek baru, mereka memilih Domain (IT atau R&D). User akan memasukkan informasi-informasi umum proyek seperti nama, deksripsi, timeline, tujuan, dsb.Sistem kemudian memunculkan _Mini-Questionnaire (Triage)_ berupa _dropdown_ untuk menentukan Risk\_Level proyek tersebut secara otomatis.
    
2.  Setelah proyek berhasil dikategorikan level risikonya (Low, Medium, High, atau Critical), proyek akan terdaftar dengan unique ID sebagai identifier proyek.
    
3.  Setelah proyek berhasil terdaftar, user pemilik proyek dapat memilih opsi menambahkan anggota proyek lain (user lain) apabila ingin memberikan akses user lain melakukan perumusan strategi mitigasi dengan mencari email dari user yang hendak ditambahkan.
    
4.  Setelah user lain sebagai expert ditambahkan, pemilik proyek bisa meng-enable proyek untuk dilakukan perumusan strategi mitigasi oleh aggota proyek lain.
    
5.  Pemilik proyek dapat melakukan pairwise comparison untuk melakukan pembobotan suara tiap expert.
    

Detail skor karakteristik proyek untuk kategorisasi level risiko:

IT:

Org\_Process\_Maturity

Low: Optimizing (1)

Medium: Optimizing (2)

High: Ad-hoc (3)

Critical: Ad-hoc (4)

Team\_Experience\_Level

Low: Expert (1)

Medium: Senior (2)

High: Mixed (3)

Critical: Junior (4)

Tech\_Environment\_Stability

Low: Modern/Stable (1)

Medium: Modern/Stable (2)

High: Legacy/Unstable (3)

Critical: Legacy/Unstable (4)

Interval Level Risiko

**Low:** 3 - 5

**Medium:** 6 - 7

**High:** 8 - 9

**Medium:** 10 - 12

R&D:

Org\_Process\_Maturity

Low: Optimizing (1)

Medium: Optimizing (1)

High: Defined (2)

Critical: Ad-hoc (4)

Project\_Manager\_Experience

Low: Certified PM (1)

High: Mid-level PM (3)

Critical: Junior PM (4)

Requirement\_Stability

Medium: Stable (1)

High: Moderate (2)

Critical: Volatile (3)

Industry\_Volatility

Low: Stable (1)

High: High (3)

Critical: Extreme (4)

Interval Level Risiko

**Low:** 3 - 5

**Medium:** 6 - 8

**High:** 9 - 11

**Medium:** 12 - 15

Kategorikan risiko berdasarkan jumlah total dari skor tiap karakteristik risiko proyek tersebut.

**Fitur 2: Pembobotan Expert**

1.  Pemilik proyek melakukan pairwise comparison untuk melakukan pembobotan kepada setiap expert yang sudah ditambahkan ke dalam proyek tersebut termasuk pemilik proyek itu sendiri.
    
2.  Hasil total bobot harus sama dengan 1.
    
3.  Setelah pembobotan dilakukan, total bobot harus sama dengan 1.
    
4.  Apabila pemilik proyek tidak melakukan pembobotan, maka bobot setiap expert diasumsikan sama.
    

**Fitur 3: Perumusan Strategi Mitigasi**

1.  User (baik yang mendaftarkan proyek ini maupun user lain yang sudah ditambahkan oleh pembuat proyek) dapat melakukan perumusan strategi mitigasi.
    
2.  Perumusan strategi mitigasi dilakukan dengan mencari proyek yang dimaksud (bisa dengan nama proyek atau unique ID).
    
3.  Setelah proyek ditemukan, menu perumusan strategi mitigasi dapat dipilih.
    
4.  Apabila proyek telah di-enable untuk dilakukan perumusan strategi oleh pemilik, maka proses dapat dilanjutkan ke poin 6. Apabila belum maka ke poin 5.
    
5.  Apabila proyek belum di-enable, munculkan notifikasi bahwa proyek belum di-enable oleh pemilik proyek untuk dilakukan perumusan strategi, hubungi pemilik proyek dan tampilkan nama dan emailnya.
    
6.  Setelah menu dipilih, user akan diberikan form AHP (skala 1-9) dengan elemen berupa 9 faktor risiko sesuai dengan level risiko proyek tersebut.
    
7.  User melakukan pairwise comparison.
    
8.  Setelah seluruh user melakukan pairwise comparison, sistem menghitung hasil pairwise comparison tersebut.
    
9.  Apabila hanya ada 1 user yang ada dalam proyek tersebut, maka hitung dengan metode AHP pada umumnya.
    
10.  Apabila ada lebih dari 1 user, maka kalkulasi AHP merujuk pada paper penelitian dengan judul “a web-based software for group decision with analytic hierarchy process” yang sudah dilampirkan.
    
11.  Setelah sistem mendapatkan bobot dari tiap elemen AHP atau tiap faktor risiko, sistem merumuskan strategi mitigasi sesuai bobot yang dihasilkan dari proses AHP. Ini dilakukan untuk memberikan rekomendasi strategi mitigasi yang sesuai dengan prioritas user.
    
12.  Hasil rekomendasi strategi mitigasi risiko disimpan ke dalam database dengan tanggal spesifik kapan perumusan ini dilakukan.
    
13.  Fitur ini dapat dilakukan lebih dari 1 kali untuk 1 proyek.