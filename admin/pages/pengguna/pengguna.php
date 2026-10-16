<?php
// Halaman daftar pengguna (hanya pelanggan dan pemilik, admin tidak ditampilkan).
// $conn = koneksi database, dibuat di index.php

// 1. Ambil pengguna selain admin (khusus pelanggan dan pemilik), dikelompokkan berdasarkan role
$hasil = $conn->query("SELECT id, nama, email, role FROM users WHERE role IN ('pelanggan', 'pemilik') ORDER BY role, nama");

$status = $_GET['status'] ?? '';
$pesan  = $_GET['pesan'] ?? '';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Kelola Pengguna</h1>
    <a href="index.php?page=pengguna-tambah" class="btn btn-sm btn-primary">+ Tambah Pengguna</a>
</div>

<?php if ($pesan != '') { ?>
    <div class="alert alert-<?php echo ($status == 'sukses') ? 'success' : 'danger'; ?>">
        <?php echo htmlspecialchars($pesan); ?>
    </div>
<?php } ?>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>

                    <!-- 2. Ulangi baris tabel untuk setiap pengguna -->
                    <?php while ($row = $hasil->fetch_assoc()) { ?>

                        <?php
                        // Warna label role
                        $warna = ($row['role'] == 'pemilik') ? 'warning' : 'info';
                        ?>

                        <tr>
                            <td><?php echo $no; ?></td>
                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><span class="badge badge-<?php echo $warna; ?>"><?php echo $row['role']; ?></span></td>
                            <td>
                                <form action="function/pengguna_delete.php" method="POST" style="display:inline"
                                      onsubmit="return confirm('Yakin hapus pengguna ini?')">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>

                        <?php $no++; ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>