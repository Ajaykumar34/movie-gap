<?php

session_start();

if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !==true)
{
    header("location: login.php");
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie list</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.2/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#">Movie Gap</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavDropdown">
    <ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
      </li>
     
      <li>
        <a class="nav-link" href="logout.php">Logout</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="login.php">Movie List</a>
      </li>

      
     
    </ul>

  <div class="navbar-collapse collapse">
  <ul class="navbar-nav ml-auto">
  <li class="nav-item active">
        <a class="nav-link" href="#"> <img src="https://img.icons8.com/metro/26/000000/guest-male.png"> <?php echo "Welcome ". $_SESSION['username']?></a>
      </li>
  </ul>
  </div>


  </div>
</nav>    

<div class="container mt-3 text-center">
        <div class="row justify-content-center">
            <div class="col-md-8 col-12 content">
                <h3>Search Movie</h3>
                <div id="search">
                    <form>
                        <input type="text" class="search-input search border-padding" placeholder="Enter name of the movie">
                    </form>
                    <div id="result-list" class="border-padding mb-3">
                        <div id="results"></div>
                        <div id="result-footer" class="pt-4 mt-2"><a href="#" id="show-more">SHOW MORE »</a></div>
                    </div>
                </div>
            </div>
            <div id="list" class="col-md-12">
                <div class="row justify-content-center">
                    <div class="col-md-8 list-header">
                      <div class="text-center"><button id="searchAgain" class="btn btn-primary">Search Again</button></div>
                        <div class="d-flex justify-content-between p-3">
                            <div id="search-term"></div>
                            <div id="list-count"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="row m-3" id="list-results"></div>
                </div>
            </div>
        </div>
    </div>
    
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.2/css/bootstrap.min.css"></script>
<Script>
    $(document).ready(function() {

function highlight(word, query) {
    let check = new RegExp(query, "ig")
    return word.toString().replace(check, function(matchedText) {
        return "<u style='background-color: yellow'>" + matchedText + "</u>"
    })
}

$("#result-list").hide()
$("#list").hide()

$(".search-input").keyup(function() {
    let search = $(this).val()
    let results = ""
    if (search == "") {
        $("#result-list").hide()
        $(".search-input").removeClass("arrow").addClass("search")
    } else {
        $(".search-input").removeClass("search").addClass("arrow")
    }

    $.getJSON("https://www.omdbapi.com/?", { apikey: "1cab6533", s: search }, function(data) {
        if (data.Search !== undefined) {
            $.each(data.Search, function(index, value) {
                if (index < 2) {
                    $.getJSON("https://www.omdbapi.com/?", { apikey: "1cab6533", i: value.imdbID }, function(movieData) {
                        if (movieData) {
                            results += '<div class="result row p-1">'
                            results += '<div class="col-sm-5"><img src=' + movieData.Poster + ' style="width: 170px; height: 250px;" /></div>'
                            results += '<div class="col-sm-7 text-left">'
                            results += '<div class="movie-title">'+ highlight(movieData.Title, $(".search-input").val()) +' ('+ movieData.Year +')</div>'
                            results += '<div class="rating-div"><span class="h4 rating">'+ movieData.imdbRating +'</span>/10</div>'
                            results += '<div class="my-3">'
                            results += '<div>Language: '+ movieData.Language + '</div>'
                            results += '<div>Stars: '+ movieData.Actors.split(",").slice(0, 3) + ' | <a href="#">Show All »</a></div>'
                            results += '</div>'
                            results += '<div class="my-3">'
                            results += '<div>'+ movieData.Plot.slice(0, 100) + '... <a href="#">Details »</a></div>'
                            results += '</div>'
                            results += '</div>'
                            results += "</div>"
                            $("#results").html(results)
                            
                            if (/Mobi|Android/i.test(navigator.userAgent)) {
                                $("#results").children(".result").eq(1).hide();
                            } else {
                                $(".result").first().after("<hr>")
                            }
                        }
                    })
                }
            });
            $("#result-list").show()
        }
    });
});

$("#show-more").click(function(e) {
    e.preventDefault()
    var search = $(".search-input").val()
    let listResults = ""
    $("#search").hide()
    $("#list").show()
    $("#search-term").html("Results for: " + search)
    $.getJSON("https://www.omdbapi.com/?", { apikey: "1cab6533", s: search }, function(listData) {
        if (/Mobi|Android/i.test(navigator.userAgent)) {
            $("#list-count").html("(" + listData.totalResults + ")")
        } else {
            $("#list-count").html(listData.totalResults + " movie found")
        }
        if (listData.Search !== undefined) {
            $.each(listData.Search, function(index, value) {
                $.getJSON("https://www.omdbapi.com/?", { apikey: "1cab6533", i: value.imdbID }, function(listMovieData) {
                    if (listMovieData) {
                        listResults += '<div class="list-result col-6 p-3">'
                        listResults += '<div class="row">'
                        listResults += '<div class="col-md-6"><img src="' + listMovieData.Poster + '" style="width: 100%;" /></div>'
                        listResults += '<div class="col-md-6 text-left">'
                        listResults += '<div class="movie-title">'+ highlight(listMovieData.Title, $(".search-input").val()) +' ('+ listMovieData.Year +')</div>'
                        listResults += '<div class="rating-div"><span class="h4 rating">'+ listMovieData.imdbRating +'</span>/10</div>'
                        listResults += '<div class="my-3">'
                        listResults += '<div>Language: '+ listMovieData.Language + '</div>'
                        listResults += '<div>Stars: '+ listMovieData.Actors.split(",").slice(0, 3) + ' | <a href="#">Show All »</a></div>'
                        listResults += '</div>'
                        listResults += '<div class="my-3">'
                        listResults += '<div>'+ listMovieData.Plot.slice(0, 100) + '... <a href="#">Details »</a></div>'
                        listResults += '</div>'
                        listResults += '</div>' // col-6 end
                        listResults += "</div>" // row end
                        listResults += "</div>" // list-result col-6 end
                        $("#list-results").html(listResults)
                        $(".list-result:odd:not(:last-child)").after("<div class='col-12'><hr></div>")
                    }
                })
            });
        }
    });
});

$("#searchAgain").click(function() {
    $("#search").show()
    $("#list").hide()
    $("#result-list").hide()
    $(".search-input").val("")
});
});
</Script>
</body>
</html>