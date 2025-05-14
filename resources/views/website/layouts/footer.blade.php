<!-- resources/views/website/layouts/footer.blade.php -->

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>Computer Shop</h5>
                <p>Your one-stop shop for all computer parts and accessories.</p>
            </div>
            <div class="col-md-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('home') }}" class="text-white">Home</a></li>
                    <li><a href="{{ route('products.index') }}" class="text-white">Products</a></li>
                    <li><a href="#" class="text-white">About Us</a></li>
                    <li><a href="#" class="text-white">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Contact Us</h5>
                <address>
                    123 Computer Street<br>
                    Tech City, TC 12345<br>
                    <abbr title="Phone">P:</abbr> (123) 456-7890
                </address>
            </div>
        </div>
        <div class="text-center mt-3">
            <p>&copy; {{ date('Y') }} Computer Shop. All rights reserved.</p>
        </div>
    </div>
</footer>