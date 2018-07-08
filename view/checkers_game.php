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
    <script>
        $(document).ready(function() {
            var playerColour = "black";
            var positions = 'ECECECEC;CECECECE;ECECECEC;EEEEEEEE;EEEEEEEE;AEAEAEAE;EAEAEAEA;AEAEAEAE';
            positions = parsePositions(positions);
            var selectedPieceX = null, selectedPieceY = null;
            var board = boardInit(); drawBoard(board, positions);

            $("body")
                .on("click", "td", function () {
                    //coordinates are saved in the name of the td, example: <td name="00"></td> is the cell 00.
                    var coordinates = $(this).attr('name');
                    console.log(coordinates);
                    var x = Number(coordinates[0]), y = Number(coordinates[1]);

                    //if we click on the empty cell, we are trying to make a move
                    if ($(this).html() === '') {
                        if (selectedPieceX !== null) {
                            $.ajax({

                                //TODO ovdje sad treba napraviti da poziva neki drugi url, da validira i da salje nazad poziciju ploce
                                //mozete pogledati u Temporary folderu kako je to radilo dok nije bilo MVC-a, morate paziti da ploce budu uskladjene
                                //s klijentom, inace cete imati problema
                                url: "Temporary/CheckersServer.php",
                                data: {
                                    oldX: selectedPieceX, oldY: selectedPieceY, newX: x,  newY: y
                                },
                                success: function(data) {
                                    if(data.positions !== undefined){
                                        selectedPieceX = null;
                                        selectedPieceY = null;
                                        positions = data.positions;
                                        console.log(positions);
                                        drawBoard(board, positions);
                                    }
                                    else {
                                        alert("potez nije validan");
                                    }
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
                        var name = $(this).children().eq(0).attr('src');
                        selectedPieceX = x; selectedPieceY = y;

                        //we remove previously selected tiles on the board
                        resetBoardColors(board);

                        //and we colour the selected piece
                        board[selectedPieceX][selectedPieceY].css("background-color", "yellow");

                        if (name === 'WhitePawn.png') {
                            showValidMoves(positions, selectedPieceX, selectedPieceY, 'A');
                        }

                        else if (name === 'WhiteKing.png') {
                            showValidMoves(positions, selectedPieceX, selectedPieceY, 'B');
                        }

                        else if (name === 'BlackPawn.png') {
                            showValidMoves(positions, selectedPieceX, selectedPieceY, 'C');
                        }

                        else if (name === 'BlackKing.png') {
                            showValidMoves(positions, selectedPieceX, selectedPieceY, 'D');
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
                var board = [];

                if(playerColour === 'white') {
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

                else if(playerColour === 'black') {
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
                            board[i][j].html('');
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

            function showValidMoves(positions, x, y, c) {

                if (!validCoordinates(x, y)) {
                    return false;
                }

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
        });
    </script>
</body>
</html>
