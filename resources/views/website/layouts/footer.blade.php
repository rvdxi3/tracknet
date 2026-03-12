<style>
    .site-footer {
        background: var(--dark);
        color: rgba(255,255,255,0.65);
        padding-top: 3.5rem;
        margin-top: 4rem;
    }
    .site-footer .footer-brand {
        font-size: 1.4rem;
        font-weight: 800;
        color: #fff;
        text-decoration: none;
        letter-spacing: -0.5px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .site-footer .footer-brand i { color: var(--primary); }
    .site-footer .footer-tagline {
        font-size: 0.875rem;
        margin-top: 0.75rem;
        line-height: 1.6;
        max-width: 280px;
    }
    .site-footer h6 {
        color: #fff;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 1rem;
    }
    .site-footer ul { list-style: none; padding: 0; margin: 0; }
    .site-footer ul li { margin-bottom: 0.55rem; }
    .site-footer ul li a {
        color: rgba(255,255,255,0.55);
        text-decoration: none;
        font-size: 0.875rem;
        transition: color 0.2s;
    }
    .site-footer ul li a:hover { color: #fff; }
    .site-footer .footer-contact { list-style: none; padding: 0; margin: 0; }
    .site-footer .footer-contact li {
        display: flex;
        align-items: flex-start;
        gap: 0.6rem;
        font-size: 0.875rem;
        margin-bottom: 0.6rem;
    }
    .site-footer .footer-contact li i {
        color: var(--primary);
        margin-top: 3px;
        flex-shrink: 0;
    }
    .site-footer .social-links {
        display: flex;
        gap: 0.6rem;
        margin-top: 1.25rem;
    }
    .site-footer .social-links a {
        width: 36px;
        height: 36px;
        background: rgba(255,255,255,0.08);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255,255,255,0.6);
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .site-footer .social-links a:hover {
        background: var(--primary);
        color: #fff;
        transform: translateY(-2px);
    }
    .site-footer .footer-bottom {
        margin-top: 2.5rem;
        border-top: 1px solid rgba(255,255,255,0.08);
        padding: 1.25rem 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .site-footer .footer-bottom p {
        margin: 0;
        font-size: 0.82rem;
        color: rgba(255,255,255,0.4);
    }
    .site-footer .footer-bottom p span { color: var(--primary); }
    .site-footer .payment-icons {
        display: flex;
        gap: 0.4rem;
    }
    .site-footer .payment-icons span {
        background: rgba(255,255,255,0.08);
        border-radius: 4px;
        padding: 0.2rem 0.5rem;
        font-size: 0.72rem;
        font-weight: 600;
        color: rgba(255,255,255,0.5);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
</style>

<footer class="site-footer">
    <div class="container">
        <div class="row g-5">
            <!-- Brand + About -->
            <div class="col-md-4 col-lg-3">
                <a class="footer-brand" href="{{ route('home') }}">
                    <i class="fas fa-laptop-code"></i> TrackNet
                </a>
                <p class="footer-tagline">
                    Your one-stop shop for premium computer parts, accessories, and peripherals — built for builders.
                </p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-6 col-md-2">
                <h6>Shop</h6>
                <ul>
                    <li><a href="{{ route('products.index') }}">All Products</a></li>
                    <li><a href="{{ route('products.index') }}?sort=price_asc">Best Sellers</a></li>
                    <li><a href="{{ route('products.index') }}?sort=price_desc">Premium</a></li>
                    <li><a href="#">Deals &amp; Offers</a></li>
                    <li><a href="#">New Arrivals</a></li>
                </ul>
            </div>

            <!-- Categories -->
            <div class="col-6 col-md-2">
                <h6>Categories</h6>
                @php $footerCats = \App\Models\Category::orderBy('name')->take(6)->get(); @endphp
                <ul>
                    @foreach($footerCats as $cat)
                        <li><a href="{{ route('products.category', $cat) }}">{{ $cat->name }}</a></li>
                    @endforeach
                    @if($footerCats->isEmpty())
                        <li><a href="{{ route('products.index') }}">Browse All</a></li>
                    @endif
                </ul>
            </div>

            <!-- Support -->
            <div class="col-6 col-md-2">
                <h6>Support</h6>
                <ul>
                    @auth
                        <li><a href="{{ route('account.index') }}">My Account</a></li>
                        <li><a href="{{ route('account.orders') }}">My Orders</a></li>
                    @endauth
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Return Policy</a></li>
                    <li><a href="#">Warranty</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="col-6 col-md-3">
                <h6>Contact Us</h6>
                <ul class="footer-contact">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>143 ByteMe Street, Davao City, Philippines 8000</span>
                    </li>
                    <li>
                        <i class="fas fa-phone"></i>
                        <span>(082) 8-70000</span>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span>support@tracknet.ph</span>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <span>Mon&ndash;Sat: 8AM &ndash; 6PM</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} <span>TrackNet</span>. All rights reserved.</p>
            <div class="payment-icons">
                <span>Cash</span>
                <span>GCash</span>
                <span>PayMaya</span>
                <span>Card</span>
            </div>
        </div>
    </div>
</footer>
