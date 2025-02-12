    <style>
        /* Container Styling */
        #empty-cart-message {
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          min-height: 75vh;
          padding: 2rem;
          background: linear-gradient(135deg, #fef3c7 15%, #fff7ed 100%);
          /* ^ Subtle warm gradient background */
          border-radius: 1rem;
          /* max-width: 600px; */
          /* margin: 2rem auto;  */
          box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
          position: relative;
          overflow: hidden;
        }
      
        /* Animated Floating Shapes (Optional Decorative Elements) */
        #empty-cart-message::before,
        #empty-cart-message::after {
          content: "";
          position: absolute;
          border-radius: 50%;
          background: rgba(251, 191, 36, 0.3);
          width: 200px;
          height: 200px;
          z-index: 0;
          animation: float 6s infinite alternate ease-in-out;
        }
        /* Positioning the decorative elements */
        #empty-cart-message::before {
          top: -30px;
          left: -30px;
        }
        #empty-cart-message::after {
          bottom: -40px;
          right: -40px;
        }
      
        /* Float Animation Keyframes */
        @keyframes float {
          0% { transform: translateY(0) rotate(0deg); }
          100% { transform: translateY(20px) rotate(15deg); }
        }
      
        /* Empty Cart Icon */
        #empty-cart-message img {
          width: 220px;
          height: auto;
          position: relative; /* sits above decorative shapes */
          z-index: 1;
          animation: bounce 2.5s infinite;
        }
      
        /* Icon Bounce Keyframes */
        @keyframes bounce {
          0%, 100% { transform: translateY(0); }
          50% { transform: translateY(-8px); }
        }
      
        /* Main Message */
        #empty-cart-message p {
          position: relative;
          z-index: 1;
          font-size: 1.5rem;
          font-weight: 700;
          color: #1f2937; /* Darker gray for contrast */
          margin-top: 1rem;
          text-align: center;
        }
      
        /* Optional Secondary Message */
        #empty-cart-message p.subtitle {
          font-size: 1rem;
          font-weight: 400;
          color: #6b7280; 
          margin-top: 0.5rem;
          max-width: 400px;
          text-align: center;
          line-height: 1.4;
        }
      
        /* Call-to-Action Button */
        #empty-cart-message a {
          position: relative;
          z-index: 1;
          display: inline-block;
          margin-top: 1.5rem;
          padding: 0.75rem 2rem;
          font-size: 1rem;
          font-weight: 600;
          color: #ffffff;
          text-decoration: none;
          border-radius: 9999px;
          background: linear-gradient(to right, #f59e0b, #fbbf24);
          box-shadow: 0 4px 10px rgba(251, 191, 36, 0.4);
          transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
      
        #empty-cart-message a:hover {
          transform: translateY(-2px);
          box-shadow: 0 6px 12px rgba(251, 191, 36, 0.5);
        }
    </style>

    {{--  --}}

      <!-- A modern empty-cart icon -->
      <img 
        src="https://cdn-icons-png.flaticon.com/512/2038/2038854.png" 
        alt="Empty Cart"
      />
    
      <!-- Original "Your cart is empty!" message -->
      <p>Your cart is empty!</p>
    
      <!-- Optional extra text for clarity -->
      <p class="subtitle">
        It looks like you haven't added anything yet. Discover our collections and find the perfect items for you!
      </p>
    
      <!-- CTA button to direct users to shop -->
      <a href="/shop">Start Shopping</a>
    {{--  --}}
      