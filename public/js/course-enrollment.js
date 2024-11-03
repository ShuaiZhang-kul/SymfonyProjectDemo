document.addEventListener('DOMContentLoaded', function() {
    loadCourses();
});

let enrolledCourses = new Set();

async function loadCourses() {
    try {
        const [coursesResponse, enrolledResponse] = await Promise.all([
            fetch('/course/courses'),
            fetch('/student/enrolled-courses')
        ]);

        if (!coursesResponse.ok || !enrolledResponse.ok) {
            throw new Error('Failed to fetch data');
        }
        
        const courses = await coursesResponse.json();
        const enrolled = await enrolledResponse.json();
        
        enrolledCourses = new Set(enrolled.map(course => course.CourseID));
        
        renderCourses(courses);
    } catch (error) {
        console.error('Error loading courses:', error);
        showError('Failed to load courses. Please try again later.');
    }
}

function renderCourses(courses) {
    const courseGrid = document.getElementById('course-grid');
    courseGrid.innerHTML = ''; // 清除加载提示

    if (courses.length === 0) {
        courseGrid.innerHTML = '<p class="no-courses">No courses available at the moment.</p>';
        return;
    }

    courses.forEach(course => {
        const courseCard = createCourseCard(course);
        courseGrid.appendChild(courseCard);
    });
}

function createCourseCard(course) {
    const card = document.createElement('div');
    card.className = 'course-card';
    const isEnrolled = enrolledCourses.has(course.CourseID);
    
    card.innerHTML = `
        <div class="course-header">
            <h3>${escapeHtml(course.CourseName)}</h3>
            <span class="course-code">${escapeHtml(course.CourseCode)}</span>
        </div>
        <div class="course-info">
            <p class="description">${escapeHtml(course.Description)}</p>
            <div class="course-details">
                <p><strong>Professor:</strong> ${escapeHtml(course.professorName)}</p>
                <p><strong>Department:</strong> ${escapeHtml(course.Department)}</p>
                <p><strong>Credits:</strong> ${course.Credits}</p>
                <p><strong>Students Enrolled:</strong> ${course.studentCount}</p>
            </div>
        </div>
        <button class="enroll-btn ${isEnrolled ? 'enrolled' : ''}" 
                data-course-id="${course.CourseID}">
            ${isEnrolled ? 'Unenroll' : 'Enroll Now'}
        </button>
    `;

    const enrollButton = card.querySelector('.enroll-btn');
    enrollButton.addEventListener('click', handleEnrollment);
    
    return card;
}

async function handleEnrollment() {
    const button = this;
    const courseId = button.getAttribute('data-course-id');
    const isEnrolled = button.classList.contains('enrolled');
    const endpoint = `/course/${isEnrolled ? 'unenroll' : 'enroll'}`;
    
    try {
        button.disabled = true;
        
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                courseId: courseId
            })
        });

        const data = await response.json();

        if (response.ok) {
            if (isEnrolled) {
                enrolledCourses.delete(parseInt(courseId));
                button.classList.remove('enrolled');
                button.textContent = 'Enroll Now';
                showSuccess('Successfully unenrolled from course');
            } else {
                enrolledCourses.add(parseInt(courseId));
                button.classList.add('enrolled');
                button.textContent = 'Unenroll';
                showSuccess('Successfully enrolled in course');
            }
            
            // 更新 My Courses 列表
            const enrolledListResponse = await fetch('/student/enrolled-courses');
            if (enrolledListResponse.ok) {
                const enrolledCourses = await enrolledListResponse.json();
                updateEnrolledCoursesList(enrolledCourses);
            }
            
            // 刷新课程列表以更新学生数量
            await loadCourses();
        } else {
            showError(data.error || 'Operation failed. Please try again.');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Network error. Please check your connection.');
    } finally {
        button.disabled = false;
    }
}

// 添加更新已选课程列表的函数
function updateEnrolledCoursesList(courses) {
    const enrolledList = document.querySelector('.enrolled-courses-list');
    if (enrolledList) {
        enrolledList.innerHTML = courses.map(course => `
            <li>
                <span class="course-name">${escapeHtml(course.CourseName)}</span>
                <span class="course-code">${escapeHtml(course.CourseCode)}</span>
            </li>
        `).join('');
    }
}

// 添加成功消息显示函数
function showSuccess(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.textContent = message;
    document.body.appendChild(successDiv);
    
    setTimeout(() => {
        successDiv.remove();
    }, 3000);
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    document.body.appendChild(errorDiv);
    
    setTimeout(() => {
        errorDiv.remove();
    }, 3000);
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

async function handleLogout() {
    try {
        const response = await fetch('/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        if (response.ok) {
            // 登出成功，重定向到登录页面
            window.location.href = '/login';
        } else {
            showError('Logout failed. Please try again.');
        }
    } catch (error) {
        console.error('Error during logout:', error);
        showError('Network error during logout.');
    }
}