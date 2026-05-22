import React, { useState, useEffect, useCallback, useRef } from 'react';
import { AgGridReact } from 'ag-grid-react';
import 'ag-grid-community/styles/ag-grid.css';
import 'ag-grid-community/styles/ag-theme-quartz.css';
import './portal.css';
import PropTypes from 'prop-types';
import LoginPage from '../Login/login.jsx';
import universityIcon from './university_icon.png'; 
import { BiSearch } from "react-icons/bi";
import { BiSolidDashboard } from "react-icons/bi";
import { MdBookmarkAdded } from "react-icons/md";
import { HiPaperClip } from "react-icons/hi2";
import { RxCountdownTimer } from "react-icons/rx";
import { GrNotes } from "react-icons/gr";
import { TbSettings2 } from "react-icons/tb";
import { MdLogout } from "react-icons/md";
import { RiExpandUpDownFill } from "react-icons/ri";
import { VscBellDot } from "react-icons/vsc";
import { HiArrowPathRoundedSquare } from "react-icons/hi2";
import { MdKeyboardArrowLeft } from "react-icons/md";
import { MdKeyboardArrowRight } from "react-icons/md";
import { SlCalender } from "react-icons/sl";
import logo from './logo.png';
import table from './table.png';

const Portal = () => {

  const [expansions, setExpansions] = useState([]);

  /////////////////////////////
  //const [requestStatusVar, setReqStatusVar] = useState('');

  // eslint-disable-next-line react/prop-types
  const ReqStatusRenderer = ({ value }) => {
    let dotClass;
    //setReqStatusVar(value)
    //console.log(value)
    if (value === 'none') {
      dotClass = 'none-grey-dot';
    } else if (value === 'Live') {
      dotClass = 'live-blue-dot';
    } else if (value === 'Approved') {
      dotClass = 'accepted-green-dot';
    } else if (value === 'Rejected') {
      dotClass = 'rejected-red-dot';
    } else {
      dotClass = 'grey-dot';
    }
    return <span className={`dot ${dotClass}`}></span>;
  };

  var [course_id, setCourse_id] = useState("Null");
  var [Attendance_type, setAttendance_type] = useState("Regular");

  // var course_id = sessionStorage.getItem('CourseId');
  // course_id = "CSE101";
  
 // const course_id = sessionStorage.getItem('CourseId');

  ////////////////////////////////////////

  const [rowData, setRowData] = useState([]);
  const [colDefs, setColDefs] = useState([
    { headerName: 'Name', field: 'stu_name', headerCheckboxSelection: true, checkboxSelection: true },
  //  { headerName: 'Status', field: 'requestStatus', cellRenderer: StatusRenderer },
    { headerName: 'Registration ID', field: 'stuRegIds' },
    { headerName: 'Attendance%', field: 'attendance_percentage',
    cellStyle: params => {
      if (params.value >= 100) {
        return { color: 'green' };
      } else if (params.value <= 75) {
        return { color: 'red' };
      } else {
        return { color: 'black' };
      }
    }
   ,cellRenderer: p => <> {p.value}<span style={{ color: 'rgb(184, 184, 184)' }}>%</span>
   </> },
    { headerName: 'Semester', field: 'semester',flex: 0.9, cellRenderer: semesterRendere },
    { headerName: 'Request Status', field: 'requestStatus', cellRenderer: ReqStatusRenderer },
    { headerName: "LeaveId", field: "leave_id", hide: true,suppressToolPanel: true
   }
  ]);

  const gridRef = useRef();

  ///////////////////////////////////
  
  const exportTableAsJson = () => {

    const exportData = [];
  
    const th_regId = sessionStorage.getItem('LoggedUserId');
  
    var datetime = new Date();
    const date = datetime.toLocaleDateString('en-CA');
    var time = datetime.toISOString().slice(11, 19);
  
    gridRef.current.api.forEachNode(node => {
      const isSelected = node.isSelected() ? 'Present' : 'Absent';
      const rowData = node.data;
  
        var { requestStatus, stuRegIds, leave_id } = rowData;
  
      var rowDataWithVariables = {course_id, th_regId, isSelected, date, time, Attendance_type, stuRegIds, leave_id
      };
     // console.log(Attendance_type);
  
      exportData.push(rowDataWithVariables);
    });
  
    // Make a POST request to the server
    fetch('http://localhost:3000/dashboard', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ data: exportData })
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Failed to upload JSON data');
      }
      // Handle successful response
      console.log('JSON data uploaded successfully');
      thing();
    })
    .catch(error => {
      console.error('Error uploading JSON data:', error);
    });
  };
  
  ///////////////////////////////////

const fetchTeacherRegisteredCourses = () =>{

  var datetime = new Date();
  const date = datetime.toLocaleDateString('en-CA');
  var th_regId = sessionStorage.getItem('LoggedUserId');

 // console.log('Fetching column from server');
  fetch(`http://localhost:3000/dashboard/getTeacherRegisteredCourses?th_regId=${th_regId}&att_date=${date}`)
    .then(response => {
      if (!response.ok) {
        throw new Error('Failed to fetch data');
      }
      return response.json();
    })
    .then(data => {
      setExpansions(data); // Update the expansions state with the fetched data
    })
    .catch(error => console.error('Error fetching data:', error));
}

  //// orignal2
  useEffect(() => {
    fetchTeacherRegisteredCourses();
  }, []);

  const [data, setData] = useState([]);


  ///////////////////////////////////

  const thing = useCallback(() => {
    fetchTeacherRegisteredCourses();
    fetchStudentsData();
    window.alert("Attendance Marked");
  }, []);

/////////////////////////////////////


  const defaultColDef = {
    flex: 1,
    editable: false,
    sortable: true,
    filter: true,
    lockPosition: true,
    headerCheckboxSelectionFilteredOnly: true
  };
  
  const fetchStudentsData = () => {
    var datetime = new Date();
    //var date = datetime.toISOString().slice(0, 10);
    const date = datetime.toLocaleDateString('en-CA');
    var th_regId = sessionStorage.getItem    ('LoggedUserId');

     console.log(course_id);
     console.log(th_regId);
    
    //console.log('Fetching Students list from server');
    fetch(`http://localhost:3000/dashboard/getStudents?course_id=${course_id}&th_regId=${th_regId}&leave_date=${date}`)
    
      .then(response => {
        if (!response.ok) {
          throw new Error('Failed to fetch data');
        }
        return response.json();
      })
      .then(data => setRowData(data))
      .catch(error => console.error('Error fetching data:', error));
  }

  useEffect(() => {
    fetchStudentsData(); 
  }, []);


  const onRowSelected = useCallback(
    (event) => {
      window.alert(
        'row ' +
        event.node.data.Name +
        ' selected = ' +
        event.node.isSelected(),
      );
    },
    [],
  );


  ////Orignal
  // useEffect(() => {

  //   fetchStudentsData(); 
  //   const datetime = new Date();
  //   const date = datetime.toISOString().slice(0, 10);

  //   console.log('Fetching table list from server');
  //   fetch(`http://localhost:3000/dashboard/getStudents?course_id=${course_id}&leave_date=${date}`)
  //     .then(response => {
  //       if (!response.ok) {
  //         throw new Error('Failed to fetch data');
  //       }
  //       return response.json();
  //     })
  //     .then(data => setRowData(data))
  //     .catch(error => console.error('Error fetching data:', error));
  // }, []);

  // const onRowSelected = useCallback(
  //   (event) => {
  //     window.alert(
  //       'row ' +
  //       event.node.data.Name +
  //       ' selected = ' +
  //       event.node.isSelected(),
  //     );
  //   },
  //   [],
  // );


  // const onCellValueChanged = useCallback(
  //   (event) => {
  //     console.log(event); // access the entire event object
  //     console.log(event.data.Name); // access and print the updated row data
  //   },
  //   [],
  // );

  const handleRadioChange = (event) => {
    setAttendance_type(event.target.value);
    //console.log(Attendance_type)
  };


  useEffect(() => {
    //console.log(course_id);
  }, [course_id]);

  const changeVar = (event) => {
    course_id = event.target.value;
    setCourse_id(event.target.value);
    fetchStudentsData();
  };
  
//   return (
//     <div className="ag-theme-quartz">
//       <AgGridReact
//        ref={gridRef}
//        rowSelection="multiple"
//         defaultColDef={defaultColDef}
//         columnDefs={colDefs}
//         rowData={rowData}
//         suppressRowClickSelection="true"
//      //   onRowSelected={onRowSelected}
//      //     onCellValueChanged={onCellValueChanged}
//       />
//       <div>
//       <button onClick={exportTableAsJson}>Mark Attendance</button>
//       </div>


//      <h1>Expansions</h1>

//         <div>
      
//         {/* <select
//   name="Expsn" onChange={changeVar(event)}>
//   <option value="Nulll" disabled selected>Select Course</option>
//   {expansions.map((expansion) => (
//     <option key={expansion.courseIDS}
//       value={expansion.courseIDS}>
//       {expansion.courseOption}
//     </option>
//   ))}
//   </select>; */}


//   <select name="Expsn" onChange={changeVar}>
//   <option value="Nulll" disabled selected>Select Course</option>
//   {expansions.map((expansion) => (
//     <option key={expansion.courseIDS} value={expansion.courseIDS}>
//       {expansion.courseOption}
//     </option>
//   ))}
// </select>;
//     </div>

      
//       <div>
//     <input type="radio" id="Regular" name="att_radio_type" value="Regular" checked={Attendance_type === "Regular"}
//     onChange={handleRadioChange}/>
//     <label htmlFor="Regular">Regular</label>
//     <br/>
//     <input type="radio" id="Makeup" name="att_radio_type" value="Makeup" checked={Attendance_type === "Makeup"} onChange={handleRadioChange}
//     />
//   <label htmlFor="Makeup">Makeup</label>
// </div>

//     </div>
//   );

return (
  <div className='main-division'>
    <nav className="container">
      <ul>
        <li className='item1'>
          <a href="#" className="logo">
            <img src={universityIcon} />
          </a>
        </li>
        <li className='item2'>
          <a href="#">
            <BiSearch size={24} className="icon1" />
            <input className="search-field" type="text" placeholder="    Search" />
          </a>
        </li>
        <li className='item3'>
          <a href="#">
            <BiSolidDashboard size={24} className="icon1" />
            <span className="nav-item">Dashboard</span>
          </a>
        </li>
        <li className='item4'>
          <a href="#">
            <MdBookmarkAdded size={24} className="icon1" />
            <span className="nav-item">Mark Attendance</span>
          </a>
        </li>
        <li className='item5'>
          <a href="#">
            <HiPaperClip size={24} className="icon1" />
            <span className="nav-item">Leave Requests</span>
          </a>
        </li>
        <li className='item6'>
          <a href="#">
            <RxCountdownTimer size={24} className="icon1" />
            <span className="nav-item">Requests History</span>
          </a>
        </li>
        <li className='item7'>
          <a href="#">
            <GrNotes size={24} className="icon1" />
            <span className="nav-item">Attendance Report</span>
          </a>
        </li>
        <li className='item8'>
          <a href="#">
            <TbSettings2 size={24} className="icon1" />
            <span className="nav-item">Settings</span>
          </a>
        </li>
        <li className='item9'>
          <a href="#" className="logout">
            <MdLogout size={24} className="icon1" />
            <span className="nav-item">Log out</span>
          </a>
        </li>
      </ul>
    </nav>

    <div className='left-division'>
      <h2 id='headone'>Good Afternoon Ahmad</h2> 
      < VscBellDot size={24} id='iconbell' />
      <TbSettings2 size={24} id="iconset" />
      <h4 id='headtwo'>Welcome to Air University Attendance Portal</h4>
      <select className='course-select'  name="Expsn" onChange={changeVar}>
        <option value="Nulll">Select Course</option>
        {expansions.map((expansion) => (
        <option key={expansion.courseIDS} value={expansion.courseIDS}>
          {expansion.courseOption}
        </option>))}
      </select>        
      <select className='section-select'>
      <option>Select Section</option>
      <option>A</option>
      <option>B</option>
      </select>
      
      {/* <div className='regular'>
      <input  type="radio"  name="att_radio_type" value="Regular" checked={Attendance_type === "Regular"} onChange={handleRadioChange} />
      <label id='text' htmlFor="Regular">Regular</label>       
      </div>
      ORG
      <div className='makeup'>
      <input type="radio" name="att_radio_type" value="Makeup" checked={Attendance_type === "Makeup"} onChange={handleRadioChange} />
      <label htmlFor="Makeup">Makeup</label>
      </div> */}

<div className='regular'>
      <input 
        type="radio" 
        id="regular" 
        name="att_radio_type" 
        value="Regular" 
        checked={Attendance_type === "Regular"} 
        onChange={handleRadioChange} 
      />
      <label htmlFor="regular" className='box-label'>Regular</label>
    </div>
<div className='makeup'>
      <input 
        type="radio" 
        id="makeup" 
        name="att_radio_type" 
        value="Makeup" 
        checked={Attendance_type === "Makeup"} 
        onChange={handleRadioChange} 
      />
      <label htmlFor="makeup" className='box-label'>Makeup</label>
    </div>
       

      <div className='update'>
      <h4 id='headthree'>Update</h4>
      <HiArrowPathRoundedSquare id='iconupdate' />
      </div>

    </div>

    <div className='right-division'>

    <div className="div">
        <img
          loading="lazy"
          src="https://cdn.builder.io/api/v1/image/assets/TEMP/9c5e6d0cff2d42ab7ef6bdac292dc3e8460850eeef2f1971de6fd5bc9f3596d5?"
          className="img"
        />
        <div className="div-2">
          <div className="div-3">
            <div className="div-4">Ahmad Hassan</div>
            <div className="div-5">
              <img
                loading="lazy"
                src="https://cdn.builder.io/api/v1/image/assets/TEMP/6c4e98e6c53a41bfa20bb0c822e4c1eace5d8db0b0cb924db8b387400226c5e5?"
                className="img-2"
              />
              <div className="div-6">Mother Sickness</div>
            </div>
          </div>
          <div className="div-7">221775</div>
          <div className="div-8">
            I hope this message finds you well. Unfortunately, I must inform you
            that my mother is unwell today, requiring my immediate attention and
            care. Consequently, I am unable to attend university. I assure you
            of my commitment to catch up on any missed work promptly and provide
            necessary documentation. Your understanding in this matter is
            greatly appreciated.
          </div>
          <div className="div-9">
            <div className="div-10">12:00PM | 12/9/2023</div>
            <div className="div-11">
              <div className="div-12">Accept</div>
              <div className="div-13">Reject</div>
            </div>
          </div>
        </div>
        <img
          loading="lazy"
          src="https://cdn.builder.io/api/v1/image/assets/TEMP/dfbe45d9ca76a1ee5064f4660e1ce3273ae007e14b709aaaf559bd42d19f0192?"
          className="img"
        />
      </div>
    

    </div>

    <div className="top-division">
    <img src={table} alt="percentage" className="percentTable" />
    </div>

    <div className="bottom-division">
    <MdKeyboardArrowLeft size={20} className='leftarrow'/>
    <SlCalender size={16} className='calender' />
    <h5 className='datetext'>    Today 23 May 2024</h5>
    <MdKeyboardArrowRight size={20} className='rightarrow'/>
    <img src={logo} alt="percentage" className="percent" />
    <div className="attendance-container">
      <h5 className='present'>Present</h5>
      <h5 className='total'>Total</h5>
      <h5 >Absent</h5>
      <h5>50</h5>
      <h5>54</h5>
      <h5>4</h5>
      </div>
      
    <div className="date-container">
      <h5 >Course Code:</h5>
      <h5 className='date-con'>SE40283</h5>
      <h5 >Saved Time & Date:</h5>
      <h5 className='date-con'>No record found</h5>
      <h5 >Current Time & Date:</h5>
      <h5 className='date-con'>12:00 | 12/9/2023</h5>
    </div>
  <button className='attend-button' onClick={exportTableAsJson}>Mark Attendance</button>
    </div>

    <div className="ag-theme-quartz">
      <AgGridReact
        ref={gridRef}
        rowSelection="multiple"
        defaultColDef={defaultColDef}
        columnDefs={colDefs}
        rowData={rowData}
        suppressRowClickSelection="true"
      />
    </div>
  </div>
);

};

const StatusRenderer = ({ value }) => {
  // Custom logic for rendering status
  return <span>{}</span>;
};

const semesterRendere = ({ value }) => {
  var temp = "I";

  if(value === 1) temp = "I";
  else if(value == 2) temp = "II";
  else if(value == 3) temp = "III";
  else if(value == 4) temp = "IV";
  else if(value == 5) temp = "V";
  else if(value == 6) temp = "VI";
  else if(value == 7) temp = "VII";
  else if(value == 8) temp = "VIII";
  return <span>{temp}</span>;
};

Portal.propTypes = {
  // Define PropTypes if needed
};

StatusRenderer.propTypes = {
  value: PropTypes.string.isRequired,
};


export default Portal;