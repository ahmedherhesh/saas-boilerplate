<!DOCTYPE html>
<!-- Coding By CodingNepal - youtube.com/codingnepal -->
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>Image Editor in JavaScript | CodingNepal</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <style>
        /* Import Google font - Poppins */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            padding: 10px;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            background: #E3F2FD;
        }

        .container {
            width: 850px;
            padding: 30px 35px 35px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .container.disable .editor-panel,
        .container.disable .controls .reset-filter,
        .container.disable .controls .save-img {
            opacity: 0.5;
            pointer-events: none;
        }

        .container h2 {
            margin-top: -8px;
            font-size: 22px;
            font-weight: 500;
        }

        .container .wrapper {
            display: flex;
            margin: 20px 0;
            min-height: 335px;
        }

        .wrapper .editor-panel {
            padding: 15px 20px;
            width: 280px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .editor-panel .title {
            display: block;
            font-size: 16px;
            margin-bottom: 12px;
        }

        .editor-panel .options,
        .controls {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .editor-panel button {
            outline: none;
            height: 40px;
            font-size: 14px;
            color: #6C757D;
            background: #fff;
            border-radius: 3px;
            margin-bottom: 8px;
            border: 1px solid #aaa;
        }

        .editor-panel .filter button {
            width: calc(100% / 2 - 4px);
        }

        .editor-panel button:hover {
            background: #f5f5f5;
        }

        .filter button.active {
            color: #fff;
            border-color: #5372F0;
            background: #5372F0;
        }

        .filter .slider {
            margin-top: 12px;
        }

        .filter .slider .filter-info {
            display: flex;
            color: #464646;
            font-size: 14px;
            justify-content: space-between;
        }

        .filter .slider input {
            width: 100%;
            height: 5px;
            accent-color: #5372F0;
        }

        .editor-panel .rotate {
            margin-top: 17px;
        }

        .editor-panel .rotate button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: calc(100% / 4 - 3px);
        }

        .rotate .options button:nth-child(3),
        .rotate .options button:nth-child(4) {
            font-size: 18px;
        }

        .rotate .options button:active {
            color: #fff;
            background: #5372F0;
            border-color: #5372F0;
        }

        .wrapper .preview-img {
            flex-grow: 1;
            display: flex;
            overflow: hidden;
            margin-left: 20px;
            border-radius: 5px;
            align-items: center;
            justify-content: center;
        }

        .preview-img img {
            max-width: 490px;
            max-height: 335px;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .controls button {
            padding: 11px 20px;
            font-size: 14px;
            border-radius: 3px;
            outline: none;
            color: #fff;
            cursor: pointer;
            background: none;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .controls .reset-filter {
            color: #6C757D;
            border: 1px solid #6C757D;
        }

        .controls .reset-filter:hover {
            color: #fff;
            background: #6C757D;
        }

        .controls .choose-img {
            background: #6C757D;
            border: 1px solid #6C757D;
        }

        .controls .save-img {
            margin-left: 5px;
            background: #5372F0;
            border: 1px solid #5372F0;
        }

        @media screen and (max-width: 760px) {
            .container {
                padding: 25px;
            }

            .container .wrapper {
                flex-wrap: wrap-reverse;
            }

            .wrapper .editor-panel {
                width: 100%;
            }

            .wrapper .preview-img {
                width: 100%;
                margin: 0 0 15px;
            }
        }

        @media screen and (max-width: 500px) {
            .controls button {
                width: 100%;
                margin-bottom: 10px;
            }

            .controls .row {
                width: 100%;
            }

            .controls .row .save-img {
                margin-left: 0px;
            }
        }
    </style>
</head>

<body>
    <div class="container disable">
        <h2>Easy Image Editor</h2>
        <div class="wrapper">
            <div class="editor-panel">
                <div class="filter">
                    <label class="title">Filters</label>
                    <div class="options">
                        <button id="brightness" class="active">Brightness</button>
                        <button id="saturation">Saturation</button>
                        <button id="inversion">Inversion</button>
                        <button id="grayscale">Grayscale</button>
                    </div>
                    <div class="slider">
                        <div class="filter-info">
                            <p class="name">Brighteness</p>
                            <p class="value">100%</p>
                        </div>
                        <input type="range" value="100" min="0" max="200">
                    </div>
                </div>
                <div class="rotate">
                    <label class="title">Rotate & Flip</label>
                    <div class="options">
                        <button id="left"><i class="fa-solid fa-rotate-left"></i></button>
                        <button id="right"><i class="fa-solid fa-rotate-right"></i></button>
                        <button id="horizontal"><i class='bx bx-reflect-vertical'></i></button>
                        <button id="vertical"><i class='bx bx-reflect-horizontal'></i></button>
                    </div>
                </div>
            </div>
            <div class="preview-img">
                <img src="image-placeholder.svg" alt="preview-img">
            </div>
        </div>
        <div class="controls">
            <button class="reset-filter">Reset Filters</button>
            <div class="row">
                <input type="file" class="file-input" accept="image/*" hidden>
                <button class="choose-img">Choose Image</button>
                <button class="save-img">Save Image</button>
            </div>
        </div>
    </div>

    <script>
        let imagesCount = '{{ auth()->user()->editedImagesCount() }}'
        let subscribedImagesCount = '{{ auth()->user()->subscribed->images_count }}'
        const fileInput = document.querySelector(".file-input"),
            filterOptions = document.querySelectorAll(".filter button"),
            filterName = document.querySelector(".filter-info .name"),
            filterValue = document.querySelector(".filter-info .value"),
            filterSlider = document.querySelector(".slider input"),
            rotateOptions = document.querySelectorAll(".rotate button"),
            previewImg = document.querySelector(".preview-img img"),
            resetFilterBtn = document.querySelector(".reset-filter"),
            chooseImgBtn = document.querySelector(".choose-img"),
            saveImgBtn = document.querySelector(".save-img");

        let brightness = "100",
            saturation = "100",
            inversion = "0",
            grayscale = "0";
        let rotate = 0,
            flipHorizontal = 1,
            flipVertical = 1;

        const loadImage = () => {
            let file = fileInput.files[0];
            if (!file) return;
            previewImg.src = URL.createObjectURL(file);
            previewImg.addEventListener("load", () => {
                resetFilterBtn.click();
                document.querySelector(".container").classList.remove("disable");
            });
        }

        const applyFilter = () => {
            previewImg.style.transform = `rotate(${rotate}deg) scale(${flipHorizontal}, ${flipVertical})`;
            previewImg.style.filter =
                `brightness(${brightness}%) saturate(${saturation}%) invert(${inversion}%) grayscale(${grayscale}%)`;
        }

        filterOptions.forEach(option => {
            option.addEventListener("click", () => {
                document.querySelector(".active").classList.remove("active");
                option.classList.add("active");
                filterName.innerText = option.innerText;

                if (option.id === "brightness") {
                    filterSlider.max = "200";
                    filterSlider.value = brightness;
                    filterValue.innerText = `${brightness}%`;
                } else if (option.id === "saturation") {
                    filterSlider.max = "200";
                    filterSlider.value = saturation;
                    filterValue.innerText = `${saturation}%`
                } else if (option.id === "inversion") {
                    filterSlider.max = "100";
                    filterSlider.value = inversion;
                    filterValue.innerText = `${inversion}%`;
                } else {
                    filterSlider.max = "100";
                    filterSlider.value = grayscale;
                    filterValue.innerText = `${grayscale}%`;
                }
            });
        });

        const updateFilter = () => {
            filterValue.innerText = `${filterSlider.value}%`;
            const selectedFilter = document.querySelector(".filter .active");

            if (selectedFilter.id === "brightness") {
                brightness = filterSlider.value;
            } else if (selectedFilter.id === "saturation") {
                saturation = filterSlider.value;
            } else if (selectedFilter.id === "inversion") {
                inversion = filterSlider.value;
            } else {
                grayscale = filterSlider.value;
            }
            applyFilter();
        }

        rotateOptions.forEach(option => {
            option.addEventListener("click", () => {
                if (option.id === "left") {
                    rotate -= 90;
                } else if (option.id === "right") {
                    rotate += 90;
                } else if (option.id === "horizontal") {
                    flipHorizontal = flipHorizontal === 1 ? -1 : 1;
                } else {
                    flipVertical = flipVertical === 1 ? -1 : 1;
                }
                applyFilter();
            });
        });

        const resetFilter = () => {
            brightness = "100";
            saturation = "100";
            inversion = "0";
            grayscale = "0";
            rotate = 0;
            flipHorizontal = 1;
            flipVertical = 1;
            filterOptions[0].click();
            applyFilter();
        }

        const saveImage = () => {
            if (parseInt(imagesCount) >= parseInt(subscribedImagesCount)) {
                return window.location.href = "{{ route('dashboard') }}"
            }
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");
            canvas.width = previewImg.naturalWidth;
            canvas.height = previewImg.naturalHeight;

            ctx.filter =
                `brightness(${brightness}%) saturate(${saturation}%) invert(${inversion}%) grayscale(${grayscale}%)`;
            ctx.translate(canvas.width / 2, canvas.height / 2);
            if (rotate !== 0) {
                ctx.rotate(rotate * Math.PI / 180);
            }
            ctx.scale(flipHorizontal, flipVertical);
            ctx.drawImage(previewImg, -canvas.width / 2, -canvas.height / 2, canvas.width, canvas.height);

            const link = document.createElement("a");
            link.download = "image.jpg";
            link.href = canvas.toDataURL();
            link.click();
            fetch("{{ route('increment.edited.image') }}", {
                method: "post",
                headers: {
                    "X-CSRF-Token": '{{ csrf_token() }}'
                }
            })
            imagesCount++;
        }

        filterSlider.addEventListener("input", updateFilter);
        resetFilterBtn.addEventListener("click", resetFilter);
        saveImgBtn.addEventListener("click", saveImage);
        fileInput.addEventListener("change", loadImage);
        chooseImgBtn.addEventListener("click", () => fileInput.click());
    </script>

</body>

</html>
