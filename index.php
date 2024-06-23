<?php

error_reporting(E_ALL);

define('OPENAI_API_KEY', 'sk-anirudh-new-project-p9NS3RpF9mskdHANWrfNT3BlbkFJAwECLvwgtSaaOEiaHTD3');  // Use your actual API key

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["uploaded_file"]) && $_FILES["uploaded_file"]["error"] == 0) {
        $filename = $_FILES["uploaded_file"]["name"];
        $filetype = $_FILES["uploaded_file"]["type"];
        $filesize = $_FILES["uploaded_file"]["size"];
        
         $allowed = array(
            "txt" => "text/plain",
            "csv" => "text/csv"
        );

        // Check file extension and MIME type
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!array_key_exists($ext, $allowed) || !in_array($_FILES["uploaded_file"]["type"], $allowed)) {
            die("Error: Unsupported file format. Please upload a TXT or CSV file.");
        }

        $maxsize = 5 * 1024 * 1024;  // 5 MB
        if ($filesize > $maxsize) {
            die("Error: File size is larger than the allowed limit.");
        }

        $promptText = file_get_contents($_FILES["uploaded_file"]["tmp_name"]);
    }
    $rule="output should in an HTML format with bootstrap table which is well designed with 100% width and it is in a colorful format and the headings in bold don't add any explaination and text content in output, share only the output which asked in the input prompt";
$promptText .= " " . $rule;
    $promptText .= " " . $_POST["prompt"];

    $postData = json_encode([
        "model" => "gpt-3.5-turbo",  // Updated to the newer chat model
        "messages" => [["role" => "user", "content" => $promptText]]
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");  // Updated endpoint
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . OPENAI_API_KEY
    ]);

    $response = curl_exec($ch);
    if ($response === false) {
        echo 'Curl error: ' . curl_error($ch);
    } else {
        $decodedResponse = json_decode($response, true);
        if (isset($decodedResponse["choices"])) {
           $result = "Don't Want to See You Next Time: " . $decodedResponse["choices"][0]["message"]["content"];
        } else {
            echo "Failed to generate text. Response was: " . $response;
        }
    }
    curl_close($ch);
}
  

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Ai Powered Report Generator</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Optional theme -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap-theme.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 50px;
        }
        .form-control, .btn-primary {
            margin-bottom: 20px;
        }
        
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Upload a File and Add a Prompt</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="uploaded_file">Upload a file (.txt or .csv):</label>
                <input type="file" id="uploaded_file" name="uploaded_file" class="form-control-file" required>
            </div>
            <div class="form-group">
                <label for="prompt">Add additional prompt text:</label>
                <textarea id="prompt" name="prompt" rows="4" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Generate Text</button>
        </form>
        <!-- Output display area -->
        <div class="container">
            <div class="row">
                <div id="output" class="col-12">
            <strong>Hello :</strong> <span id="generatedText"><?php echo $result; ?></span>
        </div>
        <div class="col-12">
            <button onclick="printOutput()" class="btn btn-info">Print Output</button>
        </div>
            </div>
        </div>
        
    </div>

    <!-- Optional JavaScript -->
    <script>
        function printOutput() {
            var content = document.getElementById('output').innerHTML;
            var originalContent = document.body.innerHTML;
            document.body.innerHTML = content;
            window.print();
            document.body.innerHTML = originalContent;
        }</script>
    <script>
        function displayGeneratedText(text) {
    document.getElementById('generatedText').innerText = text;
    document.getElementById('output').style.display = 'block'; // Show the output div
}
    </script>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>