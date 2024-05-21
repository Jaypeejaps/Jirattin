
document.addEventListener("DOMContentLoaded", function () {
    var currentURL = window.location.href;

    var dashboardURL = "dashboard.php";
    var ticketsURL = "ticketlist.php";
    var timelineURL = "timeline.php";
    var timelinetURL = "timelinee.php";
    var accomplishmentsURL = "myassign.php";
    var accomplishdoneURL = "accomplishments.php";

    var pageTitleElement = document.getElementById("page-title");

    if (currentURL.includes(dashboardURL)) {
        document.getElementById("dashboard-link").classList.add("active");
        pageTitleElement.textContent = "Dashboard";
    } else if (currentURL.includes(ticketsURL)) {
        document.getElementById("tickets-link").classList.add("active");
        pageTitleElement.textContent = "Tickets";
    } else if (currentURL.includes(timelineURL)) {
        document.getElementById("timeline-link").classList.add("active");
        pageTitleElement.textContent = "Timeline (Job)";
    } else if (currentURL.includes(timelinetURL)) {
        document.getElementById("timeline-link").classList.add("active");
        pageTitleElement.textContent = "Timeline (Task)";
    } else if (currentURL.includes(accomplishmentsURL)) {
        document.getElementById("accomplishments-link").classList.add("active");
        pageTitleElement.textContent = "My Assigned";
    } else if (currentURL.includes(accomplishdoneURL)) {
        document.getElementById("accomplishdone-link").classList.add("active");
        pageTitleElement.textContent = "Accomplishments";
    }
});
