<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/png" href="/apple-favicon.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Buy Monora Gen1 - Monora Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/src/style.css" />
    <link rel="stylesheet" href="../assets/css/header.css" />
    <link rel="stylesheet" href="../assets/css/footer.css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<header class="apple-header">
    <div class="header-container">
        <a href="../views/dashboard.php" class="apple-logo">Monora</a>
        <nav class="main-nav">
            <a href="#">Store</a>
            <a href="#">New Arrivals</a>
            <a href="#">Sales</a>
            <a href="#">Support</a>
        </nav>
        <nav class="utility-nav">
            <a href="../views/bag.php"><i class="fas fa-shopping-bag" style="color: black;"></i></a>
            <a href="../api/auth/signout.php"><i class="fas fa-sign-out-alt" style="color: black;"></i></a>
            <a href="../views/account.php"><i class="fas fa-user" style="color: black;"></i></a>
        </nav>
    </div>
</header>
  
  <body class="font-apple antialiased text-apple-dark-gray bg-white">
    
    <div id="app">
      <!-- Product Path -->
      <div class="pt-16 bg-apple-light-gray">
        <div class="max-w-5xl mx-auto px-4 py-3 text-xs">
          <ul class="flex space-x-2 items-center text-apple-gray">
            <li><a href="../views/dashboard.php" class="hover:text-apple-blue">Home</a></li>
            <li>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-3 w-3"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5l7 7-7 7"
                />
              </svg>
            </li>
            <li>
            </li>
            <li class="font-semibold text-apple-dark-gray">
              Buy Monora Gen1
            </li>
          </ul>
        </div>
      </div>

      <!-- Main Product Container -->
      <main class="bg-apple-light-gray pb-16">
        <div class="max-w-5xl mx-auto px-4 pt-4 pb-12">
          <h1 class="text-4xl md:text-5xl font-semibold mb-2">
            Buy Monora Gen1
          </h1>
          <p class="text-apple-gray">For $799.00 only!</p>
        </div>

        <div class="max-w-7xl mx-auto px-4 pb-12">
          <div class="flex flex-col md:flex-row gap-8">
            <!-- Product Image -->
            <div class="md:w-1/2 sticky top-20 h-fit">
              <div
                class="bg-white rounded-2xl p-8 shadow-apple animate-fade-in"
              >
                <div class="relative overflow-hidden pb-8">
                  <img
                    id="product-image"
                    src="/Monora/assets/images/mainPhone.png"
                    alt="Monora Gen1"
                    class="w-full transition-all duration-700 ease-in-out hover:scale-105"
                  />
                </div>
              </div>
            </div>

            <!-- Product Configuration -->
            <div class="md:w-1/2">
              <!-- Model Selection -->
              <div
                class="bg-white rounded-2xl p-8 mb-6 shadow-apple animate-fade-in"
              >
                <h2 class="text-2xl font-semibold mb-6">Model</h2>
                <div class="grid grid-cols-1 gap-4">
                  <button
                    class="model-button border-2 border-apple-blue text-center p-4 rounded-xl bg-apple-blue/5 hover:bg-apple-blue/10 transition-colors"
                  >
                    <div class="font-medium mb-1">Monora Gen1</div>
                    <div class="text-sm text-apple-gray">Standard model</div>
                    <div class="text-sm font-semibold mt-2">From $799.00</div>
                  </button>
                </div>
              </div>

              <!-- Color Selection -->
              <div
                class="bg-white rounded-2xl p-8 mb-6 shadow-apple animate-fade-in"
                style="animation-delay: 0.1s"
              >
                <h2 class="text-2xl font-semibold mb-2">Finish</h2>
                <p class="text-apple-gray text-sm mb-6">Pick your favorite.</p>

                <div class="grid grid-cols-4 gap-4 mb-6">
                  <button
                    id="color-black"
                    class="color-option flex flex-col items-center active"
                  >
                    <div
                      class="w-8 h-8 rounded-full bg-gray-200 border-2 border-white ring-2 ring-apple-blue"
                    ></div>
                    <span class="text-xs mt-2">White</span>
                  </button>
                  <button
                    id="color-white"
                    class="color-option flex flex-col items-center"
                  >
                    <div
                      class="w-8 h-8 rounded-full border-2 border-white"  style="background-color:rgb(209, 173, 11);"
                    ></div>
                    <span class="text-xs mt-2">Gold</span>
                  </button>
                  <button
                    id="color-blue"
                    class="color-option flex flex-col items-center"
                  >
                    <div
                      class="w-8 h-8 rounded-full border-2 border-white"
                      style="background-color: #1a237e;"
                    ></div>
                    <span class="text-xs mt-2">Navy Blue</span>
                  </button>
                  <button
                    id="color-green"
                    class="color-option flex flex-col items-center"
                  >
                    <div
                      class="w-8 h-8 rounded-full bg-black border-2 border-white"
                    ></div>
                    <span class="text-xs mt-2">Black</span>
                  </button>
                </div>
              </div>
              <!-- Payment Options -->
              <div
                class="bg-white rounded-2xl p-8 mb-6 shadow-apple animate-fade-in"
                style="animation-delay: 0.3s"
              >
                <h2 class="text-2xl font-semibold mb-6">Payment Options</h2>

                <div
                  class="flex border border-gray-300 rounded-xl overflow-hidden mb-6"
                >
                  <button
                    id="btn-full-payment"
                    class="payment-option w-1/1 py-3 font-medium text-center bg-gray-100 text-apple-dark-gray"
                  >
                    Pay in full
                  </button>
                </div>

                <div id="full-payment" class="payment-details">
                  <div class="flex justify-between mb-4">
                    <span class="text-apple-gray">Monora Gen1 64GB</span>
                    <span class="font-medium">$799.00</span>
                  </div>
                  <hr class="border-gray-200 my-4" />
                  <div class="flex justify-between mb-1">
                    <span class="font-semibold">Your new phone</span>
                    <span class="font-semibold">$799.00</span>
                  </div>
                </div>

                <div id="monthly-payment" class="payment-details hidden">
                  <div class="bg-apple-light-gray p-4 rounded-lg mb-4">
                    <div class="flex justify-between mb-2">
                      <span class="text-apple-gray">Monora Gen1 64GB</span>
                      <span class="font-medium">$799.00</span>
                    </div>
                    <div class="flex justify-between mb-2">
                      <span class="text-apple-gray"
                        >24 monthly installments</span
                      >
                      <span class="font-medium">at $33.29/mo.*</span>
                    </div>
                  </div>
                  <p class="text-2xs text-apple-gray mb-4">
                    *Installment pricing requires a credit card. Learn about
                    financing options.
                  </p>
                  <hr class="border-gray-200 my-4" />
                  <div class="flex justify-between mb-1">
                    <span class="font-semibold">Your new phone</span>
                    <span class="font-semibold">$33.29/mo.</span>
                  </div>
                </div>
              </div>

                <!-- Replace the existing Add to Bag button with this -->
                <div class="mb-6 animate-fade-in" style="animation-delay: 0.4s">
                  <form action="bag.php" method="get">
                    <input type="hidden" name="add" value="1"> 
                    <button type="submit" class="w-full bg-apple-blue hover:bg-apple-blue-hover text-black font-semibold py-3 px-4 rounded-full transition-colors duration-300 border border-apple-blue cursor-pointer">
                      Add to Bag
                    </button>
                  </form>
                </div>

              <!-- Product Info -->
              <div
                class="bg-white rounded-2xl p-8 shadow-apple animate-fade-in"
                style="animation-delay: 0.5s"
              >
                <div class="flex items-start space-x-5 mb-6">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-8 w-8 text-apple-gray"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="1.5"
                      d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"
                    />
                  </svg>
                  <div>
                    <h3 class="font-semibold">Free delivery</h3>
                    <p class="text-sm text-apple-gray">
                      Order now for free delivery within 3-5 business days
                    </p>
                  </div>
                </div>

                <div class="flex items-start space-x-5 mb-6">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-8 w-8 text-apple-gray"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="1.5"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                    />
                  </svg>
                  <div>
                    <h3 class="font-semibold">Free and easy returns</h3>
                    <p class="text-sm text-apple-gray">
                      Return your phone within 14 days of receiving it.
                    </p>
                  </div>
                </div>

                <div class="flex items-start space-x-5">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-8 w-8 text-apple-gray"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="1.5"
                      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>
                  <div>
                    <h3 class="font-semibold">Need help?</h3>
                    <p class="text-sm text-apple-gray">
                      Visit our
                      <a href="#" class="text-apple-blue">help center</a> or
                      <a href="#" class="text-apple-blue">contact us</a>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Features Section -->
        <div class="max-w-7xl mx-auto px-4 py-12">
          <h2 class="text-3xl font-semibold mb-12 text-center">
            Explore Monora Gen1
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div
              class="bg-white rounded-2xl p-8 shadow-apple hover:shadow-apple-hover transition-shadow duration-300"
            >
              <div class="h-48 flex items-center justify-center mb-6">
                <img
                  src="/Monora/assets/images/mainPhone.png"
                  alt="Monora Gen1 Chip"
                  class="max-h-full"
                />
              </div>
              <h3 class="text-xl font-semibold mb-2">Powerful Performance</h3>
              <p class="text-apple-gray">
                The first generation Note model by Monora with a powerful processor for smooth performance.
              </p>
            </div>

            <div
              class="bg-white rounded-2xl p-8 shadow-apple hover:shadow-apple-hover transition-shadow duration-300"
            >
              <div class="h-48 flex items-center justify-center mb-6">
                <img
                  src="/Monora/assets/images/mainPhone.png"
                  alt="Camera"
                  class="max-h-full"
                />
              </div>
              <h3 class="text-xl font-semibold mb-2">Advanced Camera System</h3>
              <p class="text-apple-gray">
                High-quality camera system for capturing your best moments with clarity.
              </p>
            </div>

            <div
              class="bg-white rounded-2xl p-8 shadow-apple hover:shadow-apple-hover transition-shadow duration-300"
            >
              <div class="h-48 flex items-center justify-center mb-6">
                <img
                  src="/Monora/assets/images/mainPhone.png"
                  alt="Battery"
                  class="max-h-full"
                />
              </div>
              <h3 class="text-xl font-semibold mb-2">All-day battery life</h3>
              <p class="text-apple-gray">
                Long-lasting battery to keep you connected throughout the day.
              </p>
            </div>
          </div>
        </div>
      </main>
    </div>

    <footer class="apple-footer">
      <div class="footer-container">
        <div class="footer-content">
          <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Use</a>
            <a href="#">Sales Policy</a>
            <a href="#">Legal</a>
            <a href="#">Site Map</a>
          </div>
          <div class="copyright">
            Copyright © 2025 Monora Inc. All rights reserved.
          </div>
        </div>
      </div>
    </footer>
  </body>
<script src="/Monora/assets/js/product_details.js"></script>
</html>