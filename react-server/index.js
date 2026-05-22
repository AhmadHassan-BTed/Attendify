// adding depedencies
const express = require('express')
const bodyParser = require('body-parser');
const app = express();
const mysql = require('mysql')
const cors = require('cors')
const fs = require('fs');

app.use(express.json())
app.use(cors ())
app.use(bodyParser.json());

const port = 3000;

app.get('/', 
  (req, res) => {
    res.send(' <h1> This is the server <h1>');
  } 
);

const db = mysql.createConnection({
  user: 'root',
  host: 'localhost',
  password: '', 
  database: 'air_attendance_portal_byted',
});

db.connect((err) => {
  if (err) {
      console.error('Error connecting to the database:', err.message);
  } else {
      console.log('Connected to the database successfully.');
  }
});

app.post('/register', (req, res) => {
    const setEmail = req.body.Email
    const setUsername = req.body.UserName
    const setPassword = req.body.Password
    const setRegId = req.body.UserRegId

    const Values = [setRegId, setEmail, setUsername, setPassword]
    //SQL post method
   // console.log(Values);

    const SQL = 'INSERT INTO login(th_regId, l_username, l_password, l_recmail) VALUES (?,?,?,?)'


    db.query(SQL, Values, (err, result)=>{
      if(err){
          res.send(err)
      }
      else{
        console.log('User Added Successfully')
        res.send({message: 'User Added'})
      }
    })
  }
)

app.post('/login', (req, res) => {

  const sentLoginUsername = req.body.LoginUserName
  const sentLoginPassword = req.body.LoginPassword

  //SQL post method

  const SQL = 'SELECT th_regId FROM login WHERE l_username = ? && l_password = ?'

  const Values = [sentLoginUsername, sentLoginPassword]

  db.query(SQL, Values, (err, result)=>{
    if(err){
         res.send({error: err})
    }


    if(result.length > 0){
      res.send(result)
      console.log('user logged')
    }
     else{
      console.log('user not found')
       res.send({message: 'Credentials Dont Match'})
     }
  })
}
)

/////////////
//Orignal
// app.get('/dashboard/getStudents', (req, res) => {
  
//   const query1 = `
//   SELECT 
//   students.stu_name, 
//   sub_query.IDs AS stuRegIds, 
//   sub_query.attendance_percentage, 
//   2026 - students.stu_batch AS semester, 
//   sub_query.RQs AS requestStatus,
//   sub_query.lids AS leave_id
// FROM students
// LEFT JOIN (
//   SELECT 
//       studentregisteredcourses.stu_regId AS IDs,
//       (COALESCE(studentregisteredcourses.src_totalPresent, 0) / COALESCE(teacherregisteredcourses.trc_totalClassesTaken, 1)) * 100 AS attendance_percentage, 
//       COALESCE(leave_application.request_status, 'No Request') AS RQs,
//       COALESCE(leave_application.leave_id) AS lids
//   FROM studentregisteredcourses
//   INNER JOIN students ON studentregisteredcourses.stu_regId = students.stu_regId
//   LEFT JOIN leave_application ON students.stu_regId = leave_application.stu_regId
//       AND leave_application.leave_date = '2024-05-30'
//   LEFT JOIN teacherregisteredcourses ON studentregisteredcourses.course_id = teacherregisteredcourses.course_id
//       AND teacherregisteredcourses.th_regId = 'CS001'
//       AND teacherregisteredcourses.course_id = 'CSE101'
//   WHERE studentregisteredcourses.course_id = 'CSE101'
// ) AS sub_query ON students.stu_regId = sub_query.IDs
// WHERE sub_query.IDs IS NOT NULL;
// `;
// db.query(query1, (error, results) => {
//   res.json(results);
//   console.log("dataSEnt")
// });
// });


app.get('/dashboard/getStudents', (req, res) => {
  
  var course_id = req.query.course_id;
  var leave_date = req.query.leave_date;
  var th_regId = req.query.th_regId;

  // if (!leave_date || !course_id) {
  //   return res.status(400).send('Missing Data');
  // }
  
  console.log("Received courseId is :", course_id);
  console.log("Received leave_date is:", leave_date);
  console.log("Received th_regid is:", th_regId);

  const query1 = `
  SELECT 
  students.stu_name, 
  sub_query.IDs AS stuRegIds, 
  sub_query.attendance_percentage, 
  2026 - students.stu_batch AS semester, 
  sub_query.RQs AS requestStatus,
  sub_query.lids AS leave_id
FROM students
LEFT JOIN (
  SELECT 
      studentregisteredcourses.stu_regId AS IDs,
      ROUND((COALESCE(studentregisteredcourses.src_totalPresent, 0) / COALESCE(teacherregisteredcourses.trc_totalClassesTaken, 1)) * 100, 2) AS attendance_percentage, 
      COALESCE(leave_application.request_status, 'No Request') AS RQs,
      COALESCE(leave_application.leave_id) AS lids
  FROM studentregisteredcourses
  INNER JOIN students ON studentregisteredcourses.stu_regId = students.stu_regId
  LEFT JOIN leave_application ON students.stu_regId = leave_application.stu_regId
      AND leave_application.leave_date = ?
  LEFT JOIN teacherregisteredcourses ON studentregisteredcourses.course_id = teacherregisteredcourses.course_id
      AND teacherregisteredcourses.th_regId = ?
      AND teacherregisteredcourses.course_id = ?
  WHERE studentregisteredcourses.course_id = ?
) AS sub_query ON students.stu_regId = sub_query.IDs
WHERE sub_query.IDs IS NOT NULL;
`;

  db.query(query1,[leave_date, th_regId,course_id,course_id] ,(error, results) => {
    res.json(results);
    console.log("dataSEnt")
    console.log(results)
  });
});


app.get('/dashboard/getTeacherRegisteredCourses', (req, res) => {
  const th_regId = req.query.th_regId;
  const att_date = req.query.att_date;
  
  // console.log("Received th_regId:", th_regId);
  // console.log("Received att_date:", att_date);
  
  if (!th_regId || !att_date) {
    return res.status(400).send('Missing th_regId or att_date');
  }
  
  const query1 = `
    SELECT courses.course_id AS courseIDS, CONCAT(courses.course_id, ' - ', courses.course_name) AS courseOption 
    FROM (
      SELECT trc.course_id, crs.course_name 
      FROM teacherregisteredcourses trc 
      JOIN courses crs ON trc.course_id = crs.course_id 
      WHERE trc.th_regId = ?
    ) AS courses
    LEFT JOIN attendance at ON courses.course_id = at.course_id AND at.att_date = ?
    WHERE at.course_id IS NULL
  `;
  
  db.query(query1, [th_regId, att_date], (error, results) => {
    if (error) {
      console.error('Error executing query:', error);
      return res.status(500).send('Error executing query');
    }
   // console.log("Query results:", results);
    res.json(results);
  });
  
});

///////////////////////////////////////

// // Orignal
// app.get('/dashboard/nothing', (req, res) => {

//   const query = ` SELECT Name, Status FROM atten `;

//   const query1 = `
//       SELECT courses.course_id AS courseIDS, CONCAT(  courses.course_id, ' - ', courses.course_name ) AS courseOption 
//     FROM (
//         SELECT trc.course_id, crs.course_name 
//         FROM teacherregisteredcourses trc 
//         JOIN courses crs ON trc.course_id = crs.course_id 
//         WHERE trc.th_regId = 'CS001'
//     ) AS courses
//     LEFT JOIN attendance at ON courses.course_id = at.course_id AND at.att_date = '2024-04-16'
//     WHERE at.course_id IS NULL
//   `

//   db.query(query1, (error, results) => {
//     res.json(results);
//     console.log(results)
//     console.log("CoursedataSEnt")
//   });
// });

////////////////////////////////////////

app.post('/dashboard', (req, res) => {
  const data = req.body.data;
  console.log(data);

  if (!Array.isArray(data)) {
    return res.status(400).send('Invalid data format');
  }

  const query = `
    INSERT INTO attendance (course_id, th_regid, att_status, att_date, att_timerecorded, att_type, stu_regId, leave_id)
    VALUES ?`;

  const values = data.map(item => [
    item.course_id,
    item.th_regId,
    item.isSelected,
    item.date,
    item.time,
    item.Attendance_type,
    item.stuRegIds,
    item.leave_id
  ]);

  db.beginTransaction(error => {
    if (error) {
      console.error('Error starting transaction:', error);
      return res.status(500).send('Error starting transaction');
    }

    db.query(query, [values], (error, results) => {
      if (error) {
        return db.rollback(() => {
          console.error('Error inserting data into the database:', error);
          res.status(500).send('Error inserting data into the database');
        });
      }

      // Extract the unique combination of th_regId and course_id from data
      const uniqueCourses = [...new Set(data.map(item => `${item.th_regId}-${item.course_id}`))];
      const updateTeacherQueries = uniqueCourses.map(course => {
        const [th_regId, course_id] = course.split('-');
        return `
          UPDATE teacherregisteredcourses 
          SET trc_totalClassesTaken = trc_totalClassesTaken + 1 
          WHERE th_regId = "${th_regId}" 
          AND course_id = "${course_id}"`;
      });

      // Extract the unique students that were marked as present
      const presentStudents = data.filter(item => item.isSelected === 'Present');
      const uniqueStudentCourses = [...new Set(presentStudents.map(item => `${item.stuRegIds}-${item.course_id}`))];
      const updateStudentQueries = uniqueStudentCourses.map(course => {
        const [stuRegIds, course_id] = course.split('-');
        return `
          UPDATE studentregisteredcourses 
          SET src_totalPresent = src_totalPresent + 1 
          WHERE stu_RegId = "${stuRegIds}" 
          AND course_id = "${course_id}"`;
      });

      // Combine all update queries
      const allUpdateQueries = [...updateTeacherQueries, ...updateStudentQueries];

      // Execute all update queries
      const updatePromises = allUpdateQueries.map(query => {
        return new Promise((resolve, reject) => {
          db.query(query, (error, results) => {
            if (error) {
              reject(error);
            } else {
              resolve(results);
            }
          });
        });
      });

      // Wait for all updates to complete
      Promise.all(updatePromises)
        .then(() => {
          db.commit(error => {
            if (error) {
              return db.rollback(() => {
                console.error('Error committing transaction:', error);
                res.status(500).send('Error committing transaction');
              });
            }
            res.send('Data inserted and incremented successfully');
          });
        })
        .catch(error => {
          db.rollback(() => {
            console.error('Error updating:', error);
            res.status(500).send('Error updating');
          });
        });
    });
  });
});

app.listen(port, ()=> {
    console.log("listening");   
})