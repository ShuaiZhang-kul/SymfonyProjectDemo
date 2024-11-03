function handleLogout() {
    // 假设你有一个登出的路由
    window.location.href = '/logout';
} 
function openNewCourseForm() {
    document.getElementById('newCourseModal').style.display = 'block';
  }
  
function closeNewCourseForm() {
  document.getElementById('newCourseModal').style.display = 'none';
}
  
// 等待DOM加载完成后再添加事件监听器
document.addEventListener('DOMContentLoaded', () => {
  const newCourseForm = document.getElementById('newCourseForm');
  if (newCourseForm) {
    newCourseForm.onsubmit = async (e) => {
      e.preventDefault();
      
      const formData = {
        courseName: document.getElementById('courseName').value,
        courseCode: document.getElementById('courseCode').value,
        description: document.getElementById('description').value,
        credits: document.getElementById('credits').value
      };

      try {
        const response = await fetch('/course/new', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(formData)
        });

        if (response.ok) {
          window.location.reload();
        } else {
          alert('Create course failed, please try again');
        }
      } catch (error) {
        console.error('Error:', error);
        alert('An error occurred, please try again');
      }
    };
  }
});
  
  // 点击modal外部关闭
  window.onclick = function(event) {
    const modal = document.getElementById('newCourseModal');
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }