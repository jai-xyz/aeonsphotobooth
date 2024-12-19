<!DOCTYPE html>
<html>
<head>
    <title>New Event Registration</title>
</head>
<script>

</script>
<body>
    <h1>New Event Registration</h1>
    <p>Title of the Event: {{ $userRegistration->event }}</p>
    <p>Event Date: {{ $userRegistration->date . ' ' . $userRegistration->time }} </p>
    <p>Contact Person: {{ $userRegistration->contactperson }}</p>
    <p>Contact No.: {{ $userRegistration->contactno }}</p>
    <p>Email: {{ $userRegistration->email }}</p>


    <p>
        <a href="{{ $adminUrl }}">Click here</a> to view the event registration details.
    </p>
    <!-- Add other necessary details -->
</body>
</html>