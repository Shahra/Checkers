<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Checkers</title>
    <link rel="stylesheet" type="text/css" href="<?php echo __SITE_URL;?>/css/style.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
</head>
<body>
    <table id="table"></table>
    <div id="opponentName">Matija</div>
    <div id="myName">Zeljko</div>
    <button id="leaveGameButton">Leave game!</button>
    <script>
        $(document).ready(function() {
            var myColour = ""; var turn = "black"; var myName = "";
            var opponentName = ""; var positions = "";
            var selectedPieceX = null, selectedPieceY = null;
            var board;
            getBoardInfo(); setInterval(getBoardInfo, 1000);

            $("#leaveGameButton").on('click', function() {
                $.ajax({
                    url: "<?php echo __SITE_URL; ?>/index.php?rt=checkers/leaveGame",
                    data: {},
                    success: function() {},
                    error: function(xhr, status) {
                        if( status !== null ) {
                            console.log( "Greška prilikom Ajax poziva: " + status );
                        }
                    }
                });
            });

            $("body")
                .on("click", "td", function () {
                    if(myColour !== turn) {
                        alert("It's not your turn, please wait!");
                    }
                    else {
                        //coordinates are saved in the name of the td, example: <td name="00"></td> is the cell 00.
                        var coordinates = $(this).attr('name');
                        var x = Number(coordinates[0]), y = Number(coordinates[1]);

                        //if we click on the empty cell, we are trying to make a move
                        if($(this).children().eq(0).attr('src') === "view/Empty.png") {
                            if (selectedPieceX !== null) {
                                $.ajax({
                                    url: "<?php echo __SITE_URL; ?>/index.php?rt=checkers/movePiece",
                                    data: {
                                        oldX: selectedPieceX, oldY: selectedPieceY,
                                        newX: x, newY: y
                                    },
                                    success: function() {
                                    },
                                    error: function(xhr, status) {
                                        if( status !== null ) {
                                            console.log( "Greška prilikom Ajax poziva: " + status );
                                        }
                                    }
                                });
                            }
                        }

                        //else, we are selecting a piece
                        else {
                            selectedPieceX = x; selectedPieceY = y;
                            resetBoardColors(board);
                            showValidMoves(positions, selectedPieceX, selectedPieceY);
                        }
                    }
                });


            function parsePositions(positions) {
                var matrix = positions.split(';');
                for (var i = 0; i < matrix.length; i++) {
                    matrix[i] = matrix[i].split('');
                }
                return matrix;
            }

            function boardInit() {
                var table = $("#table");
                table.html('');
                var board = [];

                if(myColour === 'white') {
                    for (var i = 0; i < 8; i++) {
                        var row = $("<tr></tr>");
                        board[i] = [];
                        for (var j = 0; j < 8; j++) {
                            board[i][j] = $('<td name="' + i + j + '"></td>');
                            row.append(board[i][j]);
                        }
                        table.append(row);
                    }
                }

                else if(myColour === 'black') {
                    for (var i = 7; i >= 0; i--) {
                        var row = $("<tr></tr>");
                        board[i] = [];
                        for (var j = 7; j >= 0; j--) {
                            board[i][j] = $('<td name="' + i + j + '"></td>');
                            row.append(board[i][j]);
                        }
                        table.append(row);
                    }

                }
                return board;
            }

            function resetBoardColors(board) {
                for (var i = 0; i < 8; i++) {
                    for (var j = 0; j < 8; j++) {
                        if ((i + j) % 2 === 1) board[i][j].css("background-color", "blue");
                        else board[i][j].css("background-color", "#abcdef");
                    }
                }
            }

            function drawBoard(board, positions) {
                resetBoardColors(board);
                for (var i = 0; i < positions.length; i++) {
                    for (var j = 0; j < positions[i].length; j++) {
                        if (positions[i][j] === 'A') {
                            board[i][j].html('<img class="piece" src="view/WhitePawn.png" alt="white_pawn"/>');
                        }
                        else if (positions[i][j] === 'B') {
                            board[i][j].html('<img class="piece" src="view/WhiteKing.png" alt="white_king"/>');
                        }
                        else if (positions[i][j] === 'C') {
                            board[i][j].html('<img class="piece" src="view/BlackPawn.png" alt="black_pawn"/>');
                        }
                        else if (positions[i][j] === 'D') {
                            board[i][j].html('<img class="piece" src="view/BlackKing.png" alt="black_king"/>');
                        }
                        else if (positions[i][j] === 'E') {
                            board[i][j].html('<img class="piece" src="view/Empty.png" alt="black_king"/>');
                        }
                    }
                }
            }

            function validCoordinates(x, y) {
                if (x >= 0 && x < 8 && y >= 0 && y < 8) return true;
                else return false;
            }

            function pieceColour(c) {
                if (c === 'A' || c === 'B') return 'white';
                else if (c === 'C' || c === 'D') return 'black';
                else return 'none';
            }

            function showValidMoves(positions, x, y) {

                if(myColour !== turn) {
                    return false;
                }

                if(x === null || y === null) {
                    return false
                }

                if (!validCoordinates(x, y)) {
                    return false;
                }

                var c = positions[selectedPieceX][selectedPieceY];

                if(myColour !== pieceColour(c)) {
                    return false;
                }

                board[x][y].css("background-color", "yellow");

                if (validCoordinates(x - 1, y - 1) && pieceColour(c) === 'white') {
                    if (positions[x - 1][y - 1] === 'E') {
                        board[x - 1][y - 1].css("background-color", "lime");
                    }
                    else if (pieceColour(positions[x - 1][y - 1]) === 'black') {
                        if (validCoordinates(x - 2, y - 2) && positions[x - 2][y - 2] === 'E') {
                            board[x - 2][y - 2].css("background-color", "lime");
                        }
                    }
                }

                if (validCoordinates(x - 1, y + 1) && pieceColour(c) === 'white') {
                    if (positions[x - 1][y + 1] === 'E') {
                        board[x - 1][y + 1].css("background-color", "lime");
                    }
                    else if (pieceColour(positions[x - 1][y + 1]) === 'black') {
                        if (validCoordinates(x - 2, y + 2) && positions[x - 2][y + 2] === 'E') {
                            board[x - 2][y + 2].css("background-color", "lime");
                        }
                    }
                }

                if (validCoordinates(x + 1, y - 1) && pieceColour(c) === 'black') {
                    if (positions[x + 1][y - 1] === 'E') {
                        board[x + 1][y - 1].css("background-color", "lime");
                    }
                    else if (pieceColour(positions[x + 1][y - 1]) === 'white') {
                        if (validCoordinates(x + 2, y - 2) && positions[x + 2][y - 2] === 'E') {
                            board[x + 2][y - 2].css("background-color", "lime");
                        }
                    }
                }

                if (validCoordinates(x + 1, y + 1) && pieceColour(c) === 'black') {
                    if (positions[x + 1][y + 1] === 'E') {
                        board[x + 1][y + 1].css("background-color", "lime");
                    }
                    else if (pieceColour(positions[x + 1][y + 1]) === 'white') {
                        if (validCoordinates(x + 2, y + 2) && positions[x + 2][y + 2] === 'E') {
                            board[x + 2][y + 2].css("background-color", "lime");
                        }
                    }
                }

                //____________________________SPECIAL CASES: KING MOVEMENTS________________________________________


                if (validCoordinates(x + 1, y - 1) && c === 'B') {
                    if (positions[x + 1][y - 1] === 'E') {
                        board[x + 1][y - 1].css("background-color", "lime");
                    }
                    else if (pieceColour(positions[x + 1][y - 1]) === 'black') {
                        if (validCoordinates(x + 2, y - 2) && positions[x + 2][y - 2] === 'E') {
                            board[x + 2][y - 2].css("background-color", "lime");
                        }
                    }
                }

                if (validCoordinates(x + 1, y + 1) && c === 'B') {
                    if (positions[x + 1][y + 1] === 'E') {
                        board[x + 1][y + 1].css("background-color", "lime");
                    }
                    else if (pieceColour(positions[x + 1][y + 1]) === 'black') {
                        if (validCoordinates(x + 2, y + 2) && positions[x + 2][y + 2] === 'E') {
                            board[x + 2][y + 2].css("background-color", "lime");
                        }
                    }
                }

                if (validCoordinates(x - 1, y - 1) && c === 'D') {
                    if (positions[x - 1][y - 1] === 'E') {
                        board[x - 1][y - 1].css("background-color", "lime");
                    }
                    else if (pieceColour(positions[x - 1][y - 1]) === 'white') {
                        if (validCoordinates(x - 2, y - 2) && positions[x - 2][y - 2] === 'E') {
                            board[x - 2][y - 2].css("background-color", "lime");
                        }
                    }
                }

                if (validCoordinates(x - 1, y + 1) && c === 'D') {
                    if (positions[x - 1][y + 1] === 'E') {
                        board[x - 1][y + 1].css("background-color", "lime");
                    }
                    else if (pieceColour(positions[x - 1][y + 1]) === 'white') {
                        if (validCoordinates(x - 2, y + 2) && positions[x - 2][y + 2] === 'E') {
                            board[x - 2][y + 2].css("background-color", "lime");
                        }
                    }
                }
            }

            function getBoardInfo() {
                $.ajax({
                    url: "<?php echo __SITE_URL; ?>/index.php?rt=checkers/getBoardInfo",
                    dataType: "json",
                    data: {},
                    success: function(data) {
                        if (data !== false) {
                            turn = data.turn;
                            myColour = data.colour;
                            myName = data.myName;
                            opponentName = data.opponentName;
                            positions = parsePositions(data.positions);
                            board = boardInit(); drawBoard(board, positions);
                            resetBoardColors(board); showValidMoves(positions, selectedPieceX, selectedPieceY);
                            $("#myName").html(myName);
                            $("#opponentName").html(opponentName);
                        }
                        else {
                            window.location.replace("<?php echo __SITE_URL; ?>/index.php?rt=checkers/index");
                        }
                    },
                    error: function(status) {
                        console.log("Ajax error: getBoardInfo" + JSON.stringify(status));
                    }
                });
            }
        });

    </script>
</body>
</html>
