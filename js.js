// Update clock every second
setInterval(() => {
  const clock = document.getElementById("clock");
  const now = new Date();
  clock.textContent = now.toLocaleTimeString();
  const day = document.getElementById("day");
  day.textContent = now.toLocaleString('en-US', { weekday: 'long' }).toUpperCase();
}, 1000);

// Function to toggle between sections
function toggleSection(sectionId) {
  // Hide all sections
  const sections = ['projectsSection', 'tasksSection', 'meetingsSection', 'financesSection', 'archiveSection', 'clientsSection'];
  sections.forEach(section => {
    const element = document.getElementById(section);
    if (element) {
      element.style.display = 'none';
    }
  });

  // Show the clicked section
  const sectionToShow = document.getElementById(sectionId);
  if (sectionToShow) {
    sectionToShow.style.display = 'block';
  }
}

// Add event listeners for database buttons
document.getElementById("projectsBtn").addEventListener("click", () => {
  toggleSection('projectsSection');
});

document.getElementById("tasksBtn").addEventListener("click", () => {
  toggleSection('tasksSection');
});

document.getElementById("meetingsBtn").addEventListener("click", () => {
  toggleSection('meetingsSection');
});

document.getElementById("servicesBtn").addEventListener("click", () => {
  toggleSection('servicesSection');
});

document.getElementById("resourcesBtn").addEventListener("click", () => {
  toggleSection('resourcesSection');
});

document.getElementById("clientsBtn").addEventListener("click", () => {
  toggleSection('clientsSection');
});

document.getElementById("archiveBtn").addEventListener("click", () => {
  toggleSection('archiveSection');
});


toggleSection('tasksSection');

// Add quick task functionality
document.getElementById("addQuickTask").addEventListener("click", () => {
  const taskName = document.getElementById("taskName").value;
  if (taskName) {
    const taskList = document.querySelector(".task .list-group");
    const newTask = document.createElement("li");
    newTask.classList.add("list-group-item", "d-flex", "justify-content-between", "align-items-center");
    newTask.innerHTML = ` 
      <span class="task-name"><i class="fas fa-tasks"></i> ${taskName}</span>
      <span class="task-status not-started"><i class="fas fa-hourglass-start"></i> Not Started</span>
    `;
    taskList.appendChild(newTask);
    document.getElementById("taskName").value = '';
  }
});

// Kanban project management
function addProject(projectName, description, dueDate, priority) {
  const projectCard = document.createElement("div");
  projectCard.classList.add("project-card");
  projectCard.innerHTML = `
    <h4>${projectName}</h4>
    <p class="description">${description}</p>
    <div class="details">
      <span class="due-date">Due: ${dueDate}</span>
      <span class="priority">Priority: ${priority}</span>
    </div>
    <button class="btn btn-primary">View Project</button>
  `;

  const todoColumn = document.getElementById("todo");
  todoColumn.appendChild(projectCard);
}

// Render the calendar
const calendarDays = document.getElementById("calendarDays");
const monthTitle = document.getElementById("month-title");
let currentMonth = 11; // December
let currentYear = 2024;

function renderCalendar(month, year) {
  calendarDays.innerHTML = "";
  const daysInMonth = new Date(year, month + 1, 0).getDate();

  for (let i = 1; i <= daysInMonth; i++) {
    const dayElement = document.createElement("div");
    dayElement.classList.add("calendar-day");
    dayElement.textContent = i;
    calendarDays.appendChild(dayElement);
  }

  monthTitle.textContent = `${months[month]} ${year}`;
}

document.getElementById("prev-month").addEventListener("click", () => {
  currentMonth -= 1;
  if (currentMonth < 0) {
    currentMonth = 11;
    currentYear -= 1;
  }
  renderCalendar(currentMonth, currentYear);
});

document.getElementById("next-month").addEventListener("click", () => {
  currentMonth += 1;
  if (currentMonth > 11) {
    currentMonth = 0;
    currentYear += 1;
  }
  renderCalendar(currentMonth, currentYear);
});










