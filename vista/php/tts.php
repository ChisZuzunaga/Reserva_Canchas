<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test</title>
    <style>

        .contenedor{
            outline: 2px solid black;
            width: 100%;
            height: 20vh;
        }

        .divi-1{
            outline:3px solid aqua;
            width: 100%;
            height: 33%;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
        .divi-2{
            outline:3px solid aqua;
            width: 100%;
            height: 33%;
            display: flex;
            align-items: center;
        }
        .divi-3{
            outline:3px solid aqua;
            width: 100%;
            height: 33%;
            display: flex;
            align-items: center;
        }
        .btns{
            padding-right: 2vw;
        }

        #foto-padel{
            width: 2.5vw;
            height: 2.5vw;
        }

        #txt{
            font-size: 1.8vw;
        }
        .divi-3-05{
            width: 50%;
            display: flex;
            outline: 2px solid violet;
        }
        .divi-3-05-02{
            width: 50%;
            display: flex;
            outline: 2px solid violet;
            padding-right: 2vw;
            justify-content: space-between;
        }
    </style>
    
</head>
<body>
    
    <div class="contenedor">
        <div class="divi-1">
            <div class="btns">
                <button>Cancha A</button>
                <button>Cancha B</button>
            </div>
        </div>
        <div class="divi-2">
            <img id="foto-padel" src="../uploads/padel-icon.png">
            <span id="txt">Cancha A</span>
        </div>
        <div class="divi-3">
            <div class="divi-3-05">
                <span>SEP 02 - SEP 08</span>
            </div>
            <div class="divi-3-05-02">
                <input type="date">Fecha</input>
                <button>Retroceder</button>
                <button>Avanzar</button>
            </div>
        </div>
    </div>
</body>

</html>