
      document.addEventListener("DOMContentLoaded", function () {
        // Product data - updated with Monora Gen1 details
        const products = {
          "Monora Gen1": {
            basePrice: 799.00,
            sizes: {
              "64GB": 0,
              "128GB": 100,
              "256GB": 200,
            },
            colors: {
              "White": "/Monora/assets/images/mainPhone.png",
              "Gold": "/Monora/assets/images/mainPhoneGold.png",
              "Navy Blue": "/Monora/assets/images/mainPhoneNavyBlue.png",
              "Black": "/Monora/assets/images/mainPhoneBlack.png",
            },
          },
          "Monora Gen1 Pro": {
            basePrice: 999.00,
            sizes: {
              "128GB": 0,
              "256GB": 100,
              "512GB": 200,
            },
            colors: {
              "Black": "/Monora/assets/images/mainPhone-pro-black.png",
              "White": "/Monora/assets/images/mainPhone-pro-white.png",
              "Blue": "/Monora/assets/images/mainPhone-pro-blue.png",
              "Green": "/Monora/assets/images/mainPhone-pro-green.png",
            },
          },
        };

        // Current selection
        let currentSelection = {
          model: "Monora Gen1",
          color: "Black",
          storage: "64GB",
          payment: "full",
        };

        // DOM elements
        const productImage = document.getElementById("product-image");
        const modelButtons = document.querySelectorAll(".model-button");
        const colorOptions = document.querySelectorAll(".color-option");
        const storageOptions = document.querySelectorAll(".storage-option");
        const paymentOptions = document.querySelectorAll(".payment-option");
        const paymentDetails = document.querySelectorAll(".payment-details");
        const addToBagButton = document.getElementById("add-to-bag");

        // Format currency
        function formatCurrency(amount) {
          return "₱" + amount.toLocaleString("en-PH");
        }

        // Update price display
        function updatePrice() {
          const product = products[currentSelection.model];
          const price =
            product.basePrice + product.sizes[currentSelection.storage];
          const monthlyPrice = Math.round(price / 24 * 100) / 100; // Round to 2 decimal places

          // Update full payment display
          const fullPaymentDisplay = document.querySelector(
            "#full-payment .font-medium"
          );
          if (fullPaymentDisplay) {
            fullPaymentDisplay.textContent = formatCurrency(price);
          }

          // Update monthly payment display
          const monthlyPaymentDisplays = document.querySelectorAll(
            "#monthly-payment .font-medium"
          );
          if (monthlyPaymentDisplays.length >= 2) {
            monthlyPaymentDisplays[0].textContent = formatCurrency(price);
            monthlyPaymentDisplays[1].textContent = `at ${formatCurrency(
              monthlyPrice
            )}/mo.*`;
          }

          // Update the "Your new phone" price
          const finalPriceDisplays = document.querySelectorAll(
            ".payment-details .font-semibold:last-child"
          );
          finalPriceDisplays.forEach((display) => {
            display.textContent =
              currentSelection.payment === "full"
                ? formatCurrency(price)
                : `${formatCurrency(monthlyPrice)}/mo.`;
          });

          // Update the product name in payment details
          const productNameDisplays = document.querySelectorAll(
            ".payment-details .text-apple-gray:first-child"
          );
          productNameDisplays.forEach((display) => {
            display.textContent = `${currentSelection.model} ${currentSelection.storage}`;
          });
        }

        // Model selection
        modelButtons.forEach((button) => {
          button.addEventListener("click", function () {
            // Remove active class from all buttons
            modelButtons.forEach((btn) => {
              btn.classList.remove("border-apple-blue", "bg-apple-blue/5");
              btn.classList.add("border-gray-300");
            });

            // Add active class to clicked button
            this.classList.remove("border-gray-300");
            this.classList.add("border-apple-blue", "bg-apple-blue/5");

            // Update current selection
            currentSelection.model =
              this.querySelector("div").textContent.trim();

            // Update product image based on current color
            updateProductImage();

            // Update prices
            updatePrice();
          });
        });

        // Color selection
        colorOptions.forEach((option) => {
          option.addEventListener("click", function () {
            // Remove active ring from all color options
            colorOptions.forEach((opt) => {
              const colorCircle = opt.querySelector("div");
              colorCircle.classList.remove("ring-2", "ring-apple-blue");
            });

            // Add active ring to clicked option
            const colorCircle = this.querySelector("div");
            colorCircle.classList.add("ring-2", "ring-apple-blue");

            // Update current selection
            currentSelection.color =
              this.querySelector("span").textContent.trim();

            // Update product image
            updateProductImage();
          });
        });

        // Update product image based on current selections
        function updateProductImage() {
          const product = products[currentSelection.model];
          const colorImage = product.colors[currentSelection.color];
          if (colorImage) {
            productImage.src = colorImage;
            productImage.alt = `${currentSelection.model} ${currentSelection.color}`;
            productImage.style.display = "block"; // Ensure image is visible
          }
        }

        // Storage selection
        storageOptions.forEach((option) => {
          option.addEventListener("click", function () {
            // Remove active class from all storage options
            storageOptions.forEach((opt) => {
              opt.classList.remove("border-apple-blue", "bg-apple-blue/5");
              opt.classList.add("border-gray-300");
            });

            // Add active class to clicked option
            this.classList.remove("border-gray-300");
            this.classList.add("border-apple-blue", "bg-apple-blue/5");

            // Update current selection
            currentSelection.storage =
              this.querySelector(".font-medium").textContent.trim();

            // Update prices
            updatePrice();
          });
        });

        // Payment option toggle
        paymentOptions.forEach((option) => {
          option.addEventListener("click", function () {
            // Toggle active state
            paymentOptions.forEach((opt) => {
              opt.classList.toggle("bg-white", opt === this);
              opt.classList.toggle("bg-gray-100", opt !== this);
            });

            // Update current selection
            currentSelection.payment =
              this.id === "btn-full-payment" ? "full" : "monthly";

            // Show/hide payment details
            paymentDetails.forEach((detail) => {
              detail.classList.toggle(
                "hidden",
                (detail.id === "full-payment" &&
                  currentSelection.payment !== "full") ||
                  (detail.id === "monthly-payment" &&
                    currentSelection.payment !== "monthly")
              );
            });

            // Update prices
            updatePrice();
          });
        });

        // Add to bag functionality
        addToBagButton.addEventListener("click", function () {
          const product = products[currentSelection.model];
          const price =
            product.basePrice + product.sizes[currentSelection.storage];

          alert(
            `Added to bag:\n\n` +
              `Model: ${currentSelection.model}\n` +
              `Color: ${currentSelection.color}\n` +
              `Storage: ${currentSelection.storage}\n` +
              `Price: ${formatCurrency(price)}\n` +
              `Payment: ${
                currentSelection.payment === "full"
                  ? "Full payment"
                  : "24 monthly installments"
              }`
          );
        });

        // Initialize the page
        function initialize() {
          // Set first model as active
          modelButtons[0].click();

          // Set first color as active
          colorOptions[0].click();

          // Set first storage as active
          storageOptions[0].click();

          // Set full payment as active
          paymentOptions[0].click();
        }

        initialize();
      });
