<?php include 'header.php'; ?>

<section class="contact">
    <div class="contact-header">
        <h1>Liên Hệ</h1>
        <div class="divider">
            <i class="fas fa-phone"></i>
        </div>
    </div>

    <div class="contact-container">
        <div class="contact-info">
            <h2>Thông Tin Liên Hệ</h2>
            
            <div class="info-item">
                <i class="fas fa-map-marker-alt"></i>
                <div>
                    <h3>Địa Chỉ</h3>
                    <p>123 Đường ABC, Quận XYZ, TP.HCM</p>
                </div>
            </div>

            <div class="info-item">
                <i class="fas fa-phone-alt"></i>
                <div>
                    <h3>Điện Thoại</h3>
                    <p>0123 456 789</p>
                </div>
            </div>

            <div class="info-item">
                <i class="fas fa-envelope"></i>
                <div>
                    <h3>Email</h3>
                    <p>info@hungfoodstore.com</p>
                </div>
            </div>

            <div class="info-item">
                <i class="fas fa-clock"></i>
                <div>
                    <h3>Giờ Mở Cửa</h3>
                    <p>Thứ 2 - Chủ Nhật: 7:00 - 22:00</p>
                </div>
            </div>
        </div>

        <div class="contact-form">
            <h2>Gửi Tin Nhắn</h2>
            <form action="" method="POST">
                <div class="form-group">
                    <input type="text" name="name" required placeholder="Họ và tên">
                </div>
                <div class="form-group">
                    <input type="email" name="email" required placeholder="Email">
                </div>
                <div class="form-group">
                    <input type="tel" name="phone" required placeholder="Số điện thoại">
                </div>
                <div class="form-group">
                    <textarea name="message" required placeholder="Nội dung tin nhắn"></textarea>
                </div>
                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> Gửi Tin Nhắn
                </button>
            </form>
        </div>
    </div>

    <div class="map">
        <iframe src="https://www.google.com/maps/embed?pb=..." width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
</section>

<?php include 'footer.php'; ?> 