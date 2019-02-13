<!doctype html>
<html
    lang="en">
<head>
    <meta
        charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta
        http-equiv="X-UA-Compatible"
        content="ie=edge">
    <title>
        Testing
        Api</title>
    <link
        rel="stylesheet"
        href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form
                    action="http://localhost/ischoolapi/v1/fees/add/" method="post">
                    @csrf
                    <div class="form-group">
                        <label
                            for="">Fee ID</label>
                        <input
                            name="feeID"
                            type="text"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label
                            for="">Fee name</label>
                        <input
                            name="feeName"
                            type="text"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label
                            for="">School ID</label>
                        <input
                            name="schoolID"
                            type="text"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label
                            for="">School name</label>
                        <input
                            name="schoolName"
                            type="text"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <button
                            type="submit"
                            class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
