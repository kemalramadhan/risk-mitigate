<?php

/*
|--------------------------------------------------------------------------
| Deskripsi Faktor Risiko (Knowledge Base)
|--------------------------------------------------------------------------
| Kunci = nama DASAR faktor (tanpa kategori/nilai di belakangnya).
| Nama faktor pada KB berbentuk "<Nama_Dasar>_<Kategori>", mis.
| "Team_Experience_Level_Expert" -> dasar "Team_Experience_Level".
| Pemetaan memakai longest-prefix match (lihat KnowledgeBaseSeeder).
|
| Total: 33 faktor risiko unik.
*/

return [
    'Budget_Utilization_Rate' => 'Rasio anggaran yang dihabiskan terhadap anggaran yang direncanakan.',

    'Change_Control_Maturity' => 'Kematangan proses untuk mengelola perubahan pada ruang lingkup atau persyaratan proyek.',

    'Change_Request_Frequency' => 'Tingkat permintaan perubahan yang diajukan selama proyek.',

    'Communication_Frequency' => 'Frekuensi komunikasi formal dalam proyek.',

    'Complexity_Score' => 'Skor numerik (1-10) menunjukkan kesulitan dan kerumitan proyek.',

    'Contract_Type' => 'Jenis kontrak untuk pekerjaan atau layanan eksternal (Fixed-Price, Time & Materials, Cost-Plus, atau Hybrid). Tipe kontrak menentukan pihak mana yang menanggung risiko pembengkakan biaya.',

    'Data_Security_Requirements' => 'Ketatnya peraturan keamanan data dan privasi yang berlaku untuk proyek tersebut.',

    'Documentation_Quality' => 'Kualitas dan kelengkapan dokumentasi proyek.',

    'Estimated_Timeline_Months' => 'Proyeksi durasi proyek dalam bulan.',

    'External_Dependencies_Count' => 'Jumlah faktor eksternal terkait proyek tersebut.',

    'Industry_Volatility' => 'Tingkat kestabilan sektor industri proyek (Stable, Moderate, High, atau Extreme). Semakin bergejolak industrinya, semakin tinggi ketidakpastian eksternal yang berada di luar kendali tim.',

    'Integration_Complexity' => 'Kesulitan dalam mengintegrasikan berbagai komponen proyek, sistem, atau proses.',

    'Key_Stakeholder_Availability' => 'Kemudahan akses dan responsivitas pemangku kepentingan utama.',

    'Methodology_Used' => 'Metodologi manajemen proyek yang diterapkan tim (Agile, Scrum, Waterfall, Kanban, atau Hybrid). Ketidaksesuaian metodologi dengan karakteristik proyek dapat menghambat adaptasi terhadap perubahan.',

    'Org_Process_Maturity' => 'Tingkat kematangan proses dan standar manajemen proyek organisasi.',

    'Organizational_Change_Frequency' => 'Seberapa sering organisasi mengalami perubahan struktural atau strategis.',

    'Past_Similar_Projects' => 'Jumlah proyek serupa yang sebelumnya diselesaikan oleh organisasi atau tim tersebut.',

    'Project_Budget_USD' => 'Perkiraan total anggaran yang dialokasikan untuk proyek dalam Dolar AS.',

    'Project_Manager_Experience' => 'Tingkat pengalaman manajer proyek (Junior PM, Mid-level PM, Senior PM, atau Certified PM). Manajer dengan jam terbang rendah lebih rentan salah mengambil keputusan pada situasi berisiko tinggi.',

    'Project_Start_Month' => 'Bulan dimulainya proyek (1-12). Waktu mulai berpengaruh pada risiko musiman, misalnya proyek yang dimulai menjelang akhir tahun rentan terdampak freeze period dan pergeseran anggaran.',

    'Regulatory_Compliance_Level' => 'Tingkat pengawasan peraturan atau persyaratan kepatuhan khusus yang harus dipenuhi oleh proyek.',

    'Requirement_Stability' => 'Seberapa stabil dan terdefinisi dengan baik persyaratan proyek.',

    'Resource_Availability' => 'Persentase sumber daya yang dibutuhkan dan tersedia untuk proyek tersebut.',

    'Resource_Contention_Level' => 'Tingkat persaingan untuk sumber daya bersama di beberapa proyek dalam organisasi.',

    'Risk_Management_Maturity' => 'Kematangan proses organisasi untuk mengidentifikasi, menilai, dan mengurangi risiko.',

    'Stakeholder_Count' => 'Jumlah pemangku kepentingan yang terlibat dalam proyek.',

    'Stakeholder_Engagement_Level' => 'Tingkat partisipasi dan komitmen dari para pemangku kepentingan.',

    'Team_Colocation' => 'Pola sebaran lokasi kerja tim (Fully Colocated, Partially Colocated, Hybrid, atau Fully Remote). Semakin tersebar tim, semakin besar tantangan koordinasi, komunikasi, dan sinkronisasi kerja.',

    'Team_Experience_Level' => 'Tingkat pengalaman kolektif tim (Junior, Mixed, Senior, atau Expert). Tim yang kurang berpengalaman meningkatkan kemungkinan kesalahan teknis dan estimasi yang meleset.',

    'Team_Size' => 'Jumlah individu yang ditugaskan ke tim proyek.',

    'Tech_Environment_Stability' => 'Stabilitas infrastruktur teknis atau platform yang digunakan untuk proyek tersebut.',

    'Technical_Debt_Level' => 'Tingkat akumulasi "utang teknis" — kompromi kualitas kode atau arsitektur yang ditunda perbaikannya (dinilai pada skala 0-1). Semakin tinggi, semakin besar beban perbaikan tertunda yang memperlambat pengembangan dan menaikkan risiko kegagalan teknis.',

    'Technology_Familiarity' => 'Tingkat pemahaman tim proyek dengan teknologi yang digunakan dalam proyek.',
];
