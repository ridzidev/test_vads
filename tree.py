import os

def create_directory_tree(startpath, output_filename="tree_output.txt"):
    """
    Menjelajahi struktur direktori secara rekursif mulai dari 'startpath'
    dan menulis representasi pohon (tree) ke dalam file teks.

    Args:
        startpath (str): Direktori awal untuk memulai penjelajahan.
        output_filename (str): Nama file tempat output akan disimpan.
    """
    print(f"Memulai penjelajahan dari: {startpath}")
    print(f"Hasil akan disimpan di: {output_filename}")

    # Menggunakan list untuk menampung semua baris output
    tree_lines = []
    
    # Menambahkan nama direktori awal sebagai baris pertama
    tree_lines.append(os.path.basename(os.path.abspath(startpath)) + "/")
    
    # os.walk adalah generator yang sangat efisien untuk penjelajahan direktori rekursif
    for root, dirs, files in os.walk(startpath):
        # Menghitung kedalaman (depth) saat ini untuk menentukan indentasi
        # Jarak dari startpath
        level = root.replace(startpath, '').count(os.sep)
        indent = '│   ' * level
        
        # Penjelajahan direktori
        sub_indent = '│   ' * (level + 1)

        # 1. Tampilkan direktori/folder saat ini
        # Jika bukan direktori awal, tambahkan ke output
        if root != startpath:
             dir_name = os.path.basename(root)
             tree_lines.append(f"{indent}├── \033[94m{dir_name}/\033[0m") # \033[94m untuk warna biru di konsol (opsional)

        # 2. Tampilkan sub-direktori (folders) di bawah root saat ini
        # dirs.sort() # Opsional: Urutkan sub-direktori
        for d in dirs:
            # Gunakan f-string untuk menambahkan prefix '├── ' dan '└── ' jika ini elemen terakhir
            
            # Tentukan prefix (├── atau └──)
            if d == dirs[-1] and not files:
                # Jika ini adalah sub-direktori terakhir DAN tidak ada file, gunakan └──
                prefix = '└── '
                # Sesuaikan indentasi di level berikutnya
                sub_indent_next = '    ' * (level + 2)
            else:
                # Jika bukan, gunakan ├──
                prefix = '├── '
                # Sesuaikan indentasi di level berikutnya
                sub_indent_next = '│   ' * (level + 2)
            
            # Baris output untuk direktori
            # Kita tidak perlu menampilkan sub-direktori di sini karena os.walk akan menanganinya
            # sebagai 'root' di iterasi berikutnya. Kita hanya perlu mengindentasi file.
            pass

        # 3. Tampilkan file di direktori saat ini
        # files.sort() # Opsional: Urutkan file
        for i, f in enumerate(files):
            # Tentukan prefix: └── untuk file terakhir, ├── untuk file lainnya
            prefix = '├── '
            if i == len(files) - 1 and not dirs:
                # Kasus khusus: jika file ini adalah yang terakhir DAN tidak ada sub-direktori yang tersisa
                # Kita tidak perlu repot dengan kasus khusus karena kita mengelola indentasi relatif terhadap root saat ini
                pass

            # Indentasi untuk file
            file_indent = indent
            if root != startpath:
                # Jika ini bukan direktori root, gunakan indentasi sub_indent
                file_indent = sub_indent

            # Prefix untuk file
            # Gunakan └── jika ini file terakhir di list files dan tidak ada dirs di level yang sama (sudah terlewati)
            if i == len(files) - 1:
                file_prefix = '└── '
            else:
                file_prefix = '├── '

            # Gabungkan indentasi dan prefix
            # Gunakan format yang lebih sederhana agar outputnya konsisten
            final_indent = '│   ' * level
            if level > 0:
                # Sesuaikan indentasi untuk file agar sejajar dengan direktori sebelumnya
                # Ini adalah bagian yang paling rumit, mari kita sederhanakan:

                # Kita akan membangun ulang logic indentasi secara sederhana:
                # (Kedalaman) * '│   ' + '├── ' atau '└── '
                
                # Kita sudah punya 'root' (direktori saat ini). Kita hanya perlu tampilkan isinya.
                # Indentasi harus berdasarkan level root, ditambah 1 untuk isinya.
                
                # Menggunakan Indentasi dari Level Root + Simbol
                tree_lines.append(f"{indent}{file_prefix}{f}")


    # Menulis semua baris ke file output
    try:
        with open(output_filename, 'w', encoding='utf-8') as f:
            # Hapus kode pewarnaan ANSI sebelum menulis ke file
            cleaned_lines = [line.replace('\033[94m', '').replace('\033[0m', '') for line in tree_lines]
            f.write('\n'.join(cleaned_lines))
        print(f"\n✅ Selesai! Struktur pohon direktori berhasil disimpan ke {output_filename}")
    except Exception as e:
        print(f"\n❌ Terjadi kesalahan saat menulis ke file: {e}")


# --- Bagian Utama Aplikasi ---
if __name__ == "__main__":
    # Direktori yang akan dijelajahi
    # Ganti '.' dengan path lain jika Anda ingin menjelajahi direktori yang berbeda
    start_directory = "."
    
    # Jalankan fungsi utama
    create_directory_tree(start_directory, "tree_output.txt")