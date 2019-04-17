<?php
set_time_limit(60);

require_once '../vendor/autoload.php';

use Skyree\ColorPicker\ColorPicker;

if (isset($_POST['imageUrl'])) {
    $imageUrl = $_POST['imageUrl'];
    $resize = !empty($_POST['resize']) ? (int) $_POST['resize'] : null;
    $colorSpace = (int) $_POST['colorSpace'];
    $colorNumber = (int) $_POST['quantization'];
    $paletteSize = $_POST['paletteSize'] ?? 5;
    $algorithm = $_POST['algorithm'] ?? ColorPicker::KMEANS;

    $quantization = [];
    if ($colorSpace !== 0) {
        $quantization = [
            'colorSpace' => $colorSpace,
            'colorNumber' => $colorNumber
        ];
    }

    $colorPicker = new ColorPicker();
    $palette = $colorPicker->pick($imageUrl, $algorithm, $paletteSize, $resize, $quantization);
    $pixels = [];
    foreach ($palette as $cluster) {
        if ($cluster['count'] === 0) {
            continue;
        }
        $color = $cluster['color'];
        $pixels[] = [
            'color' => sprintf("#%02x%02x%02x", (int) $color[0], (int) $color[1], (int) $color[2]),
            'count' => $cluster['count']
        ];
    }
}

?>
<!DOCTYPE>
<html lang="en">
<head>
    <title>Colorpicker Demo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style type="text/css">
        form {
            margin-top: 50px;
        }

        .result {
            width: 500px;
            margin: auto;
        }

        .result img {
            width: 100%;
        }

        .palette {
            width: 100%;
            height: 40px;
        }

        .palette td {
            padding: 4px;
        }

        .palette .color {
            width: 100%;
            height: 32px;
            border-radius: 3px;
            float: left;
            color: #fff;
            text-shadow: 1px 1px 1px black;
            line-height: 32px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <form action="" method="POST">
        <div class="form-group row">
            <label for="imageUrl" class="col-sm-2 col-form-label">Image Url</label>
            <div class="col-sm-10">
                <input id="imageUrl" name="imageUrl" class="form-control" type="text" value="<?php echo $imageUrl ?? ''; ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="resize" class="col-sm-2 col-form-label">Resize width</label>
            <div class="col-sm-10">
                <input id="resize" name="resize" class="form-control" type="text" value="<?php echo $resize ?? 150; ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="paletteSize" class="col-sm-2 col-form-label">Palette size</label>
            <div class="col-sm-10">
                <input id="paletteSize" name="paletteSize" class="form-control" type="text" value="<?php echo $paletteSize ?? 5; ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="quantization" class="col-sm-2 col-form-label">Quantization</label>
            <div class="col-sm-2">
                <select id="colorSpace" name="colorSpace" class="form-control">
                    <option value="<?php echo \Imagick::COLORSPACE_RGB; ?>"<?php echo (isset($_POST['colorSpace']) && (int) $_POST['colorSpace'] === \Imagick::COLORSPACE_RGB) ? ' selected' : ''; ?>>RGB</option>
                    <option value="<?php echo \Imagick::COLORSPACE_LUV; ?>"<?php echo (isset($_POST['colorSpace']) && (int) $_POST['colorSpace'] === \Imagick::COLORSPACE_LUV) ? ' selected' : ''; ?>>LUV</option>
                    <option value="<?php echo \Imagick::COLORSPACE_CMYK; ?>"<?php echo (isset($_POST['colorSpace']) && (int) $_POST['colorSpace'] === \Imagick::COLORSPACE_CMYK) ? ' selected' : ''; ?>>CMYK</option>
                    <option value="0"<?php echo (isset($_POST['colorSpace']) && (int) $_POST['colorSpace'] === 0) ? ' selected' : ''; ?>>None</option>
                </select>
            </div>
            <div class="col-sm-7">
                <input id="quantization" name="quantization" class="custom-range" type="range" min="16" max="256" step="1" value="<?php echo $colorNumber ?? 64; ?>">
            </div>
            <div class="col-sm-1">
                <input type="text" id="quantizationValue" class="form-control" value="<?php echo $colorNumber ?? 64; ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="algorithm" class="col-sm-2 col-form-label">Algorithm</label>
            <div class="col-sm-10">
                <select id="algorithm" name="algorithm" class="form-control">
                    <option value="<?php echo ColorPicker::KMEANS; ?>"<?php echo (isset($_POST['algorithm']) && $_POST['algorithm'] === ColorPicker::KMEANS) ? ' selected' : ''; ?>>Kmeans</option>
                    <option value="<?php echo ColorPicker::WEIGHTED_KMEANS; ?>"<?php echo (isset($_POST['algorithm']) && $_POST['algorithm'] === ColorPicker::WEIGHTED_KMEANS) ? ' selected' : ''; ?>>Weighted Kmeans</option>
                    <option value="<?php echo ColorPicker::LOCAL_MAXIMA; ?>"<?php echo (isset($_POST['algorithm']) && $_POST['algorithm'] === ColorPicker::LOCAL_MAXIMA) ? ' selected' : ''; ?>>Local maxima</option>
                    <option value="<?php echo ColorPicker::QUANTIZATION; ?>"<?php echo (isset($_POST['algorithm']) && $_POST['algorithm'] === ColorPicker::QUANTIZATION) ? ' selected' : ''; ?>>Quantization</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-10">
                <button type="submit" class="btn btn-primary">Pick</button>
            </div>
        </div>
    </form>
    <?php if (isset($pixels)): ?>
        <div class="result">
            <img src="<?php echo $imageUrl; ?>" />
            <table class="palette" border="0">
                <tr>
                    <?php foreach ($pixels as $index => $pixel): ?>
                        <?php if ($index % 5 === 0): ?>
                            </tr><tr>
                        <?php endif; ?>
                        <td><div class="color" title="<?php echo $pixel['count']; ?>" style="background-color:<?php echo $pixel['color']; ?>"><?php echo $pixel['color']; ?></div></td>
                    <?php endforeach; ?>
                </tr>
            </table>
        </div>
    <?php endif; ?>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script type="text/javascript">
	$(function() {
		$('#quantization').on('input', function() {
			let value = $(this).val();
            $('#quantizationValue').val(value);
        });

		$('#quantizationValue').on('change', function() {
			let value = $(this).val();
			$('#quantization').val(value);
		});
    });
</script>
</body>
</html>
