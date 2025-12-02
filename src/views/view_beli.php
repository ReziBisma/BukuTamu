<div class="main-content">
        <div class="container mt-4 mb-5">
            <h3 class="fw-bold">Beli Tiket Acara</h3>
            <p>Pilih jenis tiket dan lengkapi data Anda untuk mendapatkan QR Ticket Masuk.</p>

            <?php if ($msg): ?>
                <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>

            <form method="post" class="mb-4 card card-body shadow-sm" id="formBeliTiket">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kota (Lokasi Acara)</label>
                    <input type="text" name="kota" class="form-control" placeholder="Contoh: Jakarta" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pilih Tiket</label>
                    <select name="produk" class="form-select" required>
                        <option value="">-- Pilih Tiket --</option>
                        <?php
                        if (isset($produk_query) && $produk_query) {
                            mysqli_data_seek($produk_query, 0); 
                            while ($p = mysqli_fetch_assoc($produk_query)):
                        ?>
                                <option value="<?= $p['id_produk'] ?>">
                                    <?= htmlspecialchars($p['nama_produk']) ?> - Rp<?= number_format($p['harga'], 0, ',', '.') ?>
                                </option>
                        <?php
                            endwhile;
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-success" id="tombolSubmit">
                    <span id="submitText">Buat Transaksi</span>
                    <span id="submitSpinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                </button>
            </form>

            <?php if ($qrImage): ?>
                <hr>
                <h5>ID Tamu Anda: <span class="text-primary"><?= htmlspecialchars($id_tamu_generated) ?></span></h5>
                <div class="text-center">
                    <img src="data:image/png;base64,<?= $qrImage ?>" class="img-thumbnail" width="200" height="200"
                        style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#qrModal" alt="QR Code">
                    <p class="mt-2">📌 Tap QR Code untuk memperbesar</p>
                    <!-- <p class="mt-2">📌 Tap QR Code untuk memperbesar atau scan langsung dengan HP.</p> -->
                </div>

                <a href="download_qr.php?id=<?= urlencode($id_tamu_generated) ?>" class="btn btn-primary mt-2 d-flex justify-content-center">📥 Download QR</a>

                <!-- <div class="text-center mt-4">
                    <h6>Simulasi Pembayaran:</h6>
                    <img src="assets/qris.jpg" alt="QRIS" style="max-width:250px" class="img-fluid">
                    <p class="text-muted">Bayar melalui QRIS ini (simulasi, tidak perlu benar-benar membayar).</p>
                </div> -->

                <!-- Modal QR  -->
                <div class="modal fade" id="qrModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content bg-transparent border-0">
                            <div class="modal-body text-center">
                                <img src="data:image/png;base64,<?= $qrImage ?>" class="img-fluid" alt="QR Code Besar">
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>