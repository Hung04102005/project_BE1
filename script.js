let menu = document.querySelector('#menu-bars');
let navbar = document.querySelector('.navbar');

menu.onclick = () =>{
    menu.classList.toggle('fa-times');
    navbar.classList.toggle('active');
}

window.onscroll = () =>{
    menu.classList.remove('fa-times');
    navbar.classList.remove('active');
}

document.querySelector('#search-icon').onclick = () =>{
    document.querySelector('#search-form').classList.toggle('active');
}

document.querySelector('#close').onclick = () =>{
    document.querySelector('#search-form').classList.remove('active');
}

// Hiển thị/ẩn mật khẩu
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        const input = this.previousElementSibling;
        if (input.type === 'password') {
            input.type = 'text';
            this.classList.remove('fa-eye');
            this.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            this.classList.remove('fa-eye-slash');
            this.classList.add('fa-eye');
        }
    });
});

// Thêm xử lý cho input số lượng
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        quantityInput.addEventListener('change', function() {
            let value = parseInt(this.value);
            if (isNaN(value) || value < 1) {
                this.value = 1;
            } else if (value > 99) {
                this.value = 99;
            }
        });
    }
});

$(document).ready(function() {
    // Xử lý hiển thị/ẩn form tìm kiếm
    $('.search-btn').click(function(e) {
        e.stopPropagation();
        $('.search-form').toggleClass('active');
    });

    // Đóng form khi click bên ngoài
    $(document).click(function(e) {
        if (!$(e.target).closest('.search-container').length) {
            $('.search-form').removeClass('active');
        }
    });

    // Ngăn form đóng khi click vào form
    $('.search-form').click(function(e) {
        e.stopPropagation();
    });

    // Xử lý thêm vào giỏ hàng
    $('.add-to-cart-btn').click(function(e) {
        e.preventDefault();
        
        let product_id = $(this).data('product-id');
        let quantity = $('#quantity').val();

        $.ajax({
            url: 'add_to_cart.php',
            type: 'POST',
            data: {
                product_id: product_id,
                quantity: quantity
            },
            dataType: 'json',
            success: function(response) {
                console.log("Success response:", response);
                if (response.status === 'success') {
                    $('.cart-count').text(response.cart_count);
                    alert('Đã thêm sản phẩm vào giỏ hàng!');
                } else {
                    alert('Có lỗi: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log("Error:", xhr.responseText);
                alert('Có lỗi xảy ra khi thêm vào giỏ hàng!');
            }
        });
    });

    
    // Xử lý thêm vào yêu thích
    $('.add-to-favorites').click(function(e) {
        e.preventDefault();
        
        let product_id = $(this).data('product-id');  // Lấy product_id từ data attribute của nút

        $.ajax({
            url: 'add_to_favorites.php',
            type: 'POST',
            data: {
                product_id: product_id
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('.favorites-count').text(response.favorites_count);  // Cập nhật số lượng yêu thích
                    $(this).toggleClass('fas fa-heart far fa-heart'); // Thay đổi icon yêu thích
                    alert('Đã thêm sản phẩm vào danh sách yêu thích!');
                } else {
                    alert('Có lỗi: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log("Error:", xhr.responseText);
                alert('Có lỗi xảy ra khi thêm vào danh sách yêu thích!');
            }
        });
    });
});

function decreaseQuantity() {
    let input = document.getElementById('quantity');
    if(input.value > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

function increaseQuantity() {
    let input = document.getElementById('quantity');
    input.value = parseInt(input.value) + 1;
}

console.log("Script loaded!");

$(document).ready(function() {
    $('.delete-item').click(function(e) {
        e.preventDefault();
        
        const cartId = $(this).data('cart-id');
        const cartItem = $(this).closest('.cart-item');
        
        if(confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
            $.ajax({
                url: 'delete_cart_item.php',
                type: 'POST',
                data: {
                    cart_id: cartId
                },
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        // Xóa phần tử khỏi DOM
                        cartItem.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Cập nhật tổng tiền
                            updateCartTotal();
                            
                            // Kiểm tra nếu giỏ hàng trống
                            if($('.cart-item').length === 0) {
                                $('.cart-container').html(`
                                    <div class="empty-cart">
                                        <i class="fas fa-shopping-cart"></i>
                                        <p>Giỏ hàng của bạn đang trống</p>
                                        <a href="index.php" class="continue-shopping">Tiếp tục mua sắm</a>
                                    </div>
                                `);
                            }
                        });
                    } else {
                        alert('Có lỗi xảy ra: ' + response.message);
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra khi kết nối với server');
                }
            });
        }
    });
    
    // Hàm cập nhật tổng tiền
    function updateCartTotal() {
        let total = 0;
        $('.cart-item').each(function() {
            const price = parseFloat($(this).data('price'));
            const quantity = parseInt($(this).find('.quantity').text());
            total += price * quantity;
        });
        
        // Cập nhật hiển thị tổng tiền
        $('.cart-total-amount').text(formatMoney(total) + 'đ');
    }
    
    // Hàm format tiền
    function formatMoney(amount) {
        return amount.toFixed(0).replace(/\d(?=(\d{3})+$)/g, '$&,');
    }
});

$(document).ready(function() {
    $('.delete-btn').click(function(e) {
        e.preventDefault();
        
        const cartId = $(this).data('cart-id');
        const cartItem = $(this).closest('.cart-item');
        const quantity = parseInt(cartItem.find('.quantity-input').val());
        
        if(confirm('Bạn có chắc chắn muốn xóa ' + quantity + ' sản phẩm này khỏi giỏ hàng?')) {
            $.ajax({
                url: 'delete_cart_item.php',
                type: 'POST',
                data: {
                    cart_id: cartId,
                    quantity: quantity
                },
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        if(response.remove_entire_item) {
                            // Xóa toàn bộ item
                            cartItem.fadeOut(300, function() {
                                $(this).remove();
                                updateCartTotal();
                                
                                // Kiểm tra giỏ hàng trống
                                if($('.cart-item').length === 0) {
                                    $('.cart-container').html(`
                                        <div class="empty-cart">
                                            <i class="fas fa-shopping-cart"></i>
                                            <p>Giỏ hàng của bạn đang trống</p>
                                            <a href="index.php" class="continue-shopping">Tiếp tục mua sắm</a>
                                        </div>
                                    `);
                                }
                            });
                        } else {
                            // Cập nhật số lượng mới
                            cartItem.find('.quantity-input').val(response.new_quantity);
                            updateCartTotal();
                        }
                    } else {
                        alert('Có lỗi xảy ra: ' + response.message);
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra khi kết nối với server');
                }
            });
        }
    });
    
    // Hàm cập nhật tổng tiền
    function updateCartTotal() {
        let total = 0;
        $('.cart-item').each(function() {
            const price = parseFloat($(this).data('price'));
            const quantity = parseInt($(this).find('.quantity-input').val());
            total += price * quantity;
        });
        
        $('.cart-total-amount').text(formatMoney(total) + 'đ');
    }
    
    function formatMoney(amount) {
        return amount.toFixed(0).replace(/\d(?=(\d{3})+$)/g, '$&,');
    }
});

function initializeDeleteCartItem() {
    // Thêm console.log để debug
    console.log('Initializing delete buttons...');
    console.log('Found delete buttons:', $('.delete-btn').length);

    $(document).on('click', '.delete-btn', function(e) {  // Thay đổi cách bind event
        e.preventDefault();
        console.log('Delete button clicked');
        
        const cartId = $(this).data('cart-id');
        const cartItem = $(this).closest('.cart-item');
        
        console.log('Cart ID:', cartId);
        
        if(confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
            $.ajax({
                url: 'delete_cart_item.php',
                type: 'POST',
                data: { cart_id: cartId },
                dataType: 'json',
                success: function(response) {
                    console.log('Delete response:', response);
                    if(response.status === 'success') {
                        cartItem.fadeOut(300, function() {
                            $(this).remove();
                            updateCartTotal();
                            
                            if($('.cart-item').length === 0) {
                                $('.cart-container').html(`
                                    <div class="empty-cart">
                                        <i class="fas fa-shopping-cart"></i>
                                        <p>Giỏ hàng của bạn đang trống</p>
                                        <a href="index.php" class="continue-shopping">Tiếp tục mua sắm</a>
                                    </div>
                                `);
                            }
                        });
                    } else {
                        alert('Có lỗi xảy ra: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Delete error:', error);
                    console.log('Response:', xhr.responseText);
                    alert('Có lỗi xảy ra khi kết nối với server');
                }
            });
        }
    });
}

// Thêm function mới để xử lý xóa item
function deleteCartItem(cartId) {
    if(confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
        $.ajax({
            url: 'delete_cart_item.php',
            type: 'POST',
            data: { cart_id: cartId },
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    // Tìm và xóa item khỏi DOM
                    const cartItem = $(`button[data-cart-id="${cartId}"]`).closest('.cart-item');
                    const itemTotal = parseFloat(cartItem.find('.item-total').text().replace(/[^0-9]/g, ''));
                    
                    // Lấy số lượng của sản phẩm bị xóa
                    const itemQuantity = parseInt(cartItem.find('.quantity-input').val());
                    
                    cartItem.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Cập nhật tổng tiền
                        const currentTotal = parseFloat($('.cart-total-amount').text().replace(/[^0-9]/g, ''));
                        const newTotal = currentTotal - itemTotal;
                        $('.cart-total-amount').text(newTotal.toLocaleString('vi-VN') + 'đ');
                        
                        // Cập nhật số lượng trong giỏ hàng trên header
                        let cartCount = parseInt($('.cart-count').text()) - itemQuantity;
                        $('.cart-count').text(cartCount);
                        
                        // Nếu giỏ hàng trống, ẩn số lượng
                        if(cartCount <= 0) {
                            $('.cart-count').hide();
                        }
                        
                        // Kiểm tra nếu giỏ hàng trống
                        if($('.cart-item').length === 0) {
                            $('.cart-container').html(`
                                <div class="empty-cart">
                                    <p>Giỏ hàng của bạn đang trống</p>
                                    <a href="index.php" class="continue-shopping">Tiếp tục mua sắm</a>
                                </div>
                            `);
                            // Ẩn phần tổng tiền và nút thanh toán
                            $('.cart-summary').hide();
                        }
                    });
                } else {
                    alert('Có lỗi xảy ra: ' + response.message);
                }
            },
            error: function() {
                alert('Có lỗi xảy ra khi kết nối với server');
            }
        });
    }
}

function processCheckout() {
    if(confirm('Bạn có chắc chắn muốn thanh toán giỏ hàng này?')) {
        $.ajax({
            url: 'process_checkout.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    alert('Thanh toán thành công!');
                    // Chuyển hướng đến trang cảm ơn hoặc trang chủ
                    window.location.href = 'thank_you.php';
                } else {
                    alert('Có lỗi xảy ra: ' + response.message);
                }
            },
            error: function() {
                alert('Có lỗi xảy ra khi kết nối với server');
            }
        });
    }
}
