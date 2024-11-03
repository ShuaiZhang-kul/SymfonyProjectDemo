document.addEventListener('DOMContentLoaded', function() {
    loadAllCourses();
});

async function loadAllCourses() {
    try {
        const response = await fetch('/course/courses');
        if (!response.ok) {
            throw new Error('Failed to fetch courses');
        }
        const courses = await response.json();
        renderCourses(courses);
    } catch (error) {
        console.error('Error loading courses:', error);
        showError('Failed to load courses. Please try again later.');
    }
}

function renderCourses(courses) {
    const courseGrid = document.getElementById('course-grid');
    courseGrid.innerHTML = '';

    if (courses.length === 0) {
        courseGrid.innerHTML = '<p class="no-courses">No courses available at the moment.</p>';
        return;
    }

    courses.forEach(course => {
        const courseCard = createCourseCard(course);
        courseGrid.appendChild(courseCard);
    });
}

// 定义柔和的背景色数组
const courseColors = [
    'rgba(74, 144, 226, 0.1)',   // 浅蓝色
    'rgba(80, 200, 120, 0.1)',   // 浅绿色
    'rgba(245, 166, 35, 0.1)',   // 浅橙色
    'rgba(155, 89, 182, 0.1)',   // 浅紫色
    'rgba(231, 76, 60, 0.1)',    // 浅红色
    'rgba(52, 152, 219, 0.1)',   // 浅天蓝色
    'rgba(46, 204, 113, 0.1)',   // 浅翠绿色
    'rgba(230, 126, 34, 0.1)',   // 浅橙褐色
    'rgba(142, 68, 173, 0.1)',   // 浅深紫色
    'rgba(22, 160, 133, 0.1)'    // 浅青色
];

function createCourseCard(course) {
    const card = document.createElement('div');
    card.className = 'course-card';
    const randomColor = courseColors[Math.floor(Math.random() * courseColors.length)];
    
    card.innerHTML = `
        <div class="course-header" style="background-color: ${randomColor}; padding: 16px; border-radius: 8px;">
            <h3>${escapeHtml(course.CourseName)}</h3>
            <span class="course-code">${escapeHtml(course.CourseCode)}</span>
        </div>
        <p class="course-description">${escapeHtml(course.Description)}</p>
        <div class="course-info">
            <div class="info-row">
                <span class="label">Professor:</span>
                <span class="value">${escapeHtml(course.professorName)}</span>
            </div>
            <div class="info-row">
                <span class="label">Department:</span>
                <span class="value">${escapeHtml(course.Department)}</span>
            </div>
            <div class="info-row">
                <span class="label">Credits:</span>
                <span class="value">${course.Credits}</span>
            </div>
            <div class="info-row">
                <span class="label">Students Enrolled:</span>
                <span class="value">${course.studentCount}</span>
            </div>
        </div>
    `;
    
    return card;
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
} 