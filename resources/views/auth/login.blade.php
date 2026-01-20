@extends('layouts.auth')

@section('title', 'Login')

@section('content')

<style>
  * {
    box-sizing: border-box;
  }

  body {
    overflow-x: hidden;
  }

  .login-container {
    min-height: 100vh;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    overflow: hidden;
  }

  .login-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url("{{ URL::asset('build/images/login-background.png') }}");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    filter: blur(12px) brightness(0.85);
    transform: scale(1.05);
    z-index: 0;
  }

  .login-form-wrapper {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 1000px;
    margin: 0 auto;
  }

  .login-card {
    background: #ffffff;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 25px 70px rgba(0, 0, 0, 0.35);
    backdrop-filter: blur(10px);
  }

  .login-left-section {
    padding: 0;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 650px;
    background: transparent;
    overflow: hidden;
  }

  .login-left-section img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
  }

  .login-right-section {
    padding: 50px 60px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 650px;
  }

  .login-logo {
    margin-bottom: 40px;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .login-logo img {
    max-width: 220px;
    width: 100%;
    height: auto;
    display: block;
    margin: 0 auto;
  }

  .login-form-label {
    font-weight: 500;
    color: #000000;
    margin-bottom: 10px;
    font-size: 15px;
    display: block;
  }

  .login-input-username {
    background-color: #e3f2fd;
    border: 1px solid #bbdefb;
    border-radius: 10px;
    padding: 14px 16px;
    font-size: 15px;
    width: 100%;
    transition: all 0.3s ease;
    color: #000;
  }

  .login-input-username::placeholder {
    color: #90a4ae;
  }

  .login-input-username:focus {
    background-color: #e3f2fd;
    border-color: #64b5f6;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
    outline: none;
  }

  .login-input-password {
    background-color: #ffffff;
    border: 1px solid #dee2e6;
    border-right: none;
    border-radius: 10px 0 0 10px;
    padding: 14px 16px;
    font-size: 15px;
    width: 100%;
    transition: all 0.3s ease;
    color: #000;
  }

  .login-input-password::placeholder {
    color: #90a4ae;
  }

  .login-input-password:focus {
    background-color: #ffffff;
    border-color: #86b7fe;
    border-right: none;
    box-shadow: none;
    outline: none;
    z-index: 3;
  }

  .login-input-password:focus + .login-password-toggle {
    border-color: #86b7fe;
    z-index: 3;
  }

  .login-password-toggle {
    background-color: #ffffff;
    border: 1px solid #dee2e6;
    border-left: none;
    border-radius: 0 10px 10px 0;
    padding: 14px 16px;
    cursor: pointer;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s ease;
    min-width: 50px;
  }

  .login-password-toggle:hover {
    background-color: #f8f9fa;
    color: #495057;
  }

  .login-password-toggle i {
    font-size: 18px;
  }

  .login-btn-masuk {
    background-color: #28a745;
    border: none;
    border-radius: 10px;
    padding: 16px;
    font-weight: 600;
    font-size: 16px;
    color: #ffffff;
    width: 100%;
    transition: all 0.3s ease;
    cursor: pointer;
    text-transform: none;
  }

  .login-btn-masuk:hover {
    background-color: #218838;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
  }

  .login-btn-masuk:active {
    transform: translateY(0);
  }

  .login-btn-masuk:focus {
    background-color: #218838;
    color: #ffffff;
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.3);
    outline: none;
  }

  /* Responsive Design */
  @media (max-width: 1199px) {
    .login-form-wrapper {
      max-width: 900px;
    }

    .login-right-section {
      padding: 40px 50px;
    }
  }

  @media (max-width: 991px) {
    .login-container {
      padding: 15px;
    }

    .login-form-wrapper {
      max-width: 100%;
    }

    .login-card {
      border-radius: 20px;
    }

    .login-left-section {
      min-height: 350px;
      max-height: 400px;
    }

    .login-right-section {
      padding: 35px 40px;
      min-height: auto;
    }

    .login-logo {
      margin-bottom: 30px;
    }

    .login-logo img {
      max-width: 180px;
    }
  }

  @media (max-width: 767px) {
    .login-left-section {
      display: none !important;
    }

    .col-lg-6.col-md-12.order-lg-1.order-2 {
      display: none !important;
    }

    .col-lg-6.col-md-12.order-lg-2.order-1 {
      width: 100% !important;
      flex: 0 0 100% !important;
      max-width: 100% !important;
    }

    .login-container {
      padding: 10px;
      align-items: flex-start;
      padding-top: 20px;
    }

    .login-card {
      border-radius: 16px;
    }

    .login-right-section {
      padding: 30px 25px;
    }

    .login-logo {
      margin-bottom: 25px;
    }

    .login-logo img {
      max-width: 160px;
    }

    .login-form-label {
      font-size: 14px;
      margin-bottom: 8px;
    }

    .login-input-username,
    .login-input-password {
      padding: 12px 14px;
      font-size: 14px;
    }

    .login-password-toggle {
      padding: 12px 14px;
      min-width: 45px;
    }

    .login-btn-masuk {
      padding: 14px;
      font-size: 15px;
    }
  }

  @media (max-width: 575px) {
    .login-right-section {
      padding: 25px 20px;
    }

    .login-logo img {
      max-width: 140px;
    }

    .login-left-section {
      min-height: 200px;
      max-height: 250px;
    }
  }

  @media (max-width: 400px) {
    .login-right-section {
      padding: 20px 15px;
    }
  }

  /* Fix for input group focus */
  .input-group:focus-within .login-input-password {
    border-color: #86b7fe;
    z-index: 3;
  }

  .input-group:focus-within .login-password-toggle {
    border-color: #86b7fe;
    z-index: 3;
  }
</style>

<div class="login-container">
  <div class="login-background"></div>
  
  <div class="login-form-wrapper">
    <div class="login-card">
      <div class="row g-0">
        <!-- Left Section - Safety Image -->
        <div class="col-lg-6 col-md-12 order-lg-1 order-2">
          <div class="login-left-section">
            <img src="{{ URL::asset('build/images/login-form.png') }}" alt="Safety is our culture" loading="lazy">
          </div>
        </div>

        <!-- Right Section - Login Form -->
        <div class="col-lg-6 col-md-12 order-lg-2 order-1">
          <div class="login-right-section">
            <div class="login-logo">
              <img src="{{ URL::asset('build/images/logo-removebg.png') }}" alt="beraucoal" onerror="this.src='{{ URL::asset('build/images/logo.jpg') }}'" loading="eager">
            </div>

            <form method="POST" action="{{ route('login') }}" id="loginForm">
              @csrf
              
              <div class="mb-4">
                <label for="inputEmailAddress" class="login-form-label">Username</label>
                <input type="text" 
                       class="form-control login-input-username @error('email') is-invalid @enderror" 
                       id="inputEmailAddress" 
                       name="email" 
                       value="{{ old('email') }}" 
                       placeholder="Enter Username"
                       autocomplete="username"
                       required>
                @error('email')
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>

              <div class="mb-4">
                <label for="inputChoosePassword" class="login-form-label">Password</label>
                <div class="input-group" id="show_hide_password">
                  <input type="password" 
                         class="form-control login-input-password @error('password') is-invalid @enderror" 
                         id="inputChoosePassword" 
                         name="password"
                         placeholder="Enter Password"
                         autocomplete="current-password"
                         required>
                  <a href="javascript:;" class="input-group-text login-password-toggle" aria-label="Toggle password visibility">
                    <i class="bi bi-eye-slash-fill"></i>
                  </a>
                </div>
                @error('password')
                  <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>

              <div class="mb-0">
                <button type="submit" class="btn login-btn-masuk">
                  <span>Masuk</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection