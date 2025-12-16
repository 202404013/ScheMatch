<!DOCTYPE html>
<html>
<head>
<title>ScheMatch</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
body {
            font-family: "Raleway", Arial, sans-serif;
            background-color: #e1a3b9b5;
            color: #000000ff;
        }
h1 {letter-spacing: 6px}
.w3-row-padding img {margin-bottom: 12px}
</style>
</head>
<body>

<div class="w3-content" style="max-width:1500px">

<header class="w3-panel w3-center w3-opacity" style="padding:128px 16px">
  <h1>ScheMatch</h1>
  <h1 class="w3-xlarge">Classmate Finder and Availability Matcher</h1>
  
  <div class="w3-padding-32">
    <div class="w3-bar w3-border">
      <a href="#about" class="w3-bar-item w3-button w3-light-grey">About</a>
      
      @auth
        <a href="{{ route('availabilities.index') }}" class="w3-bar-item w3-button">Availability</a>
        <a href="{{ route('courses.index') }}" class="w3-bar-item w3-button w3-hide-small">Courses</a>
        <a href="{{ route('dashboard') }}" class="w3-bar-item w3-button w3-hide-small">Home</a>
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
          @csrf
          <button type="submit" class="w3-bar-item w3-button w3-hide-small" style="border: none; background: none; cursor: pointer; font-family: 'Raleway', Arial, sans-serif;">
            Logout
          </button>
        </form>
      @else
        <a href="{{ route('login') }}" class="w3-bar-item w3-button w3-hide-small">Login</a>
        <a href="{{ route('register') }}" class="w3-bar-item w3-button w3-hide-small">Register</a>
      @endauth
    </div>
  </div>
</header>

  <div id="about">
    <div class="w3-container w3-padding-50">
      <p class="w3-panel w3-center" style="padding:128px 16px; font-size: 18px;">
      ScheMatch is an app that matches schedules to find common availability among selected users. 
      You can also connect with peers through the course matcher.
      <br>To start matching, register now!
      </p>
      
  </div>
</div>
</div>

<footer class="w3-container w3-padding-64 w3-center w3-large" 
        style="background-color: #951313ff; color: white;">
  🗓️
  <p>Project by <span class="w3-hover-text-green">Erika Margaret Vallesteros</span></p>
  <p class="w3-medium">ScheMatch 2025</p>
</footer>

</body>
</html>