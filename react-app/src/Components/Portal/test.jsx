/*
const datasource = {
  getRows(params) {
      console.log(JSON.stringify(params.request, null, 1));

      fetch('http://localhost:3000/dashboard', {
          method: 'post',
          body: JSON.stringify(params.request),
          headers: { 'Content-Type': 'application/json; charset=utf-8' }
      })
      .then(httpResponse => httpResponse.json())
      .then(response => {
          params.successCallback(response.rows, response.lastRow);
      })
      .catch(error => {
          console.error(error);
          params.failCallback();
      })
  }
};


api.setGridOption('serverSideDatasource', datasource);

*/



// const onCellValueChanged = useCallback((event) => {
//   // Use the gridRef to access the api
//   console.log(event); // access the entire event object
//   console.log(event.data.Name); // access and print the updated row data
//   const gridData = this.getAllData();  
// }, []);


const onRowSelected = useCallback(
  (event) => {
    window.alert(
        console.log("Row Selected")
    );
  },
  [window],
);


// const onSelectionChanged = useCallback(
//   (event) => {
//     console.log("selection Changed")
//   },
//   [window],
// );

////////////////////////////////////////////////

// const gridRef = useRef();

// const onClick = useCallback(() => {
//     // Use the gridRef to access the api
//     gridRef.current?.api.deselectAll();
// }, []);





// const someMethod = () => {
//   let selectedRows;
//   selectedRows = this.gridApi.getSelectedRows();
//   console.log(selectedRows);
//   ///than you can map your selectedRows 
//   selectedRows.map((row) => {
//    console.log(row);
//    console.log(row.data);
//   });
// }

///////////////////////////////////////////////////




import React, { useState, useEffect, useCallback } from 'react';
import { AgGridReact } from 'ag-grid-react';
import 'ag-grid-community/styles/ag-grid.css';
import 'ag-grid-community/styles/ag-theme-quartz.css';
import './portal.css';
import PropTypes from 'prop-types';

const Portal = () => {
  const [rowData, setRowData] = useState([]);
  const [colDefs, setColDefs] = useState([
    { headerName: 'Name', field: 'Name', headerCheckboxSelection: true, checkboxSelection: true },
    { headerName: 'Status', field: 'Status', cellRenderer: StatusRenderer },
    { headerName: 'Registration ID', field: 'Reg_Id' },
    { headerName: 'Attendance %', field: 'percentage', cellStyle: getPercentageCellStyle, cellRenderer: PercentageRenderer },
    { headerName: 'Semester', field: 'semester' },
    { headerName: 'Request Status', field: 'reqStatus', cellRenderer: ReqStatusRenderer },
  ]);

  const defaultColDef = {
    flex: 1,
    editable: false,
    sortable: true,
    filter: true,
    lockPosition: true,
    headerCheckboxSelectionFilteredOnly: true
  };

  useEffect(() => {
    console.log('Fetching table list from server');
    fetch('http://localhost:3000/dashboard')
      .then(response => {
        if (!response.ok) {
          throw new Error('Failed to fetch data');
        }
        return response.json();
      })
      .then(data => setRowData(data))
      .catch(error => console.error('Error fetching data:', error));
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

  const onCellValueChanged = useCallback(
    (event) => {
      console.log(event); // access the entire event object
      console.log(event.data.Name); // access and print the updated row data
    },
    [],
  );

  return (
    <div className="ag-theme-quartz">
      <AgGridReact
        defaultColDef={defaultColDef}
        columnDefs={colDefs}
        rowData={rowData}
        onRowSelected={onRowSelected}
        onCellValueChanged={onCellValueChanged}
      />
    </div>
  );
};

const StatusRenderer = ({ value }) => {
  // Custom logic for rendering status
  return <span>{value}</span>;
};

const ReqStatusRenderer = ({ value }) => {
  // Custom logic for rendering request status
  return <span>{value}</span>;
};

const PercentageRenderer = ({ value }) => {
  // Custom logic for rendering percentage
  return <span>{value}</span>;
};

const getPercentageCellStyle = (params) => {
  // Custom cell style logic for percentage column
  return {
    color: params.value === 100 ? 'green' : params.value <= 75 ? 'red' : 'black',
  };
};

Portal.propTypes = {
  // Define PropTypes if needed
};

StatusRenderer.propTypes = {
  value: PropTypes.string.isRequired,
};

ReqStatusRenderer.propTypes = {
  value: PropTypes.string.isRequired,
};

PercentageRenderer.propTypes = {
  value: PropTypes.number.isRequired,
};

export default Portal;





//////////////////////////


//import {useState, useEffect, useCallback, useRef} from 'react'
//import { AgGridReact} from 'ag-grid-react'; // AG Grid Component
import "ag-grid-community/styles/ag-grid.css"; // Mandatory CSS required by the grid
import "ag-grid-community/styles/ag-theme-quartz.css"; // Optional Theme applied to the grid
import './portal.css'
//import PropTypes from 'prop-types';
//import { ColDef, GridApi, ColumnApi } from 'ag-grid-community';
// import { CsvExportParams } from 'ag-grid-community';
//import Axios from 'axios'

import "ag-grid-charts-enterprise";

const portal = () => {
  
const [requestStatusVar, setReqStatusVar] = useState('');

// if conditions
const ReqStatusRenderer = ({ value }) => {
  let dotClass;
  setReqStatusVar(value)
  if (value === 'none') {
    dotClass = 'none-grey-dot';
  } else if (value === 'Live') {
    dotClass = 'live-blue-dot';
  } else if (value === 'accepted') {
    dotClass = 'accepted-green-dot';
  } else if (value === 'rejected') {
    dotClass = 'rejected-red-dot';
  } else {
    dotClass = 'grey-dot';
  }
  return <span className={`dot ${dotClass}`}></span>;
};

const StatusRenderer = ({value}) => {
  let dotClass;
  if (value === 'unmarked') {
    dotClass = 'none-grey-dot';
  } else if (requestStatusVar === 'present') {
    dotClass = 'live-blue-dot';
  } else if (requestStatusVar === 'absent') {
    dotClass = 'accepted-green-dot';
  } else if (value === 'rejected') {
    dotClass = 'rejected-red-dot';
  } else {
    dotClass = 'grey-dot';
  }
  return <span className={`dot ${dotClass}`}> {requestStatusVar}</span>;
};

ReqStatusRenderer.propTypes = {
  value: PropTypes.string.isRequired
};


const onRowSelected = useCallback(
  (event) => {
    window.alert(
      "row " +
        event.node.data.Name +
        " selected = " +
        event.node.isSelected(),
    );
  },
  [window],
);

const onSelectionChanged = useCallback(
  (event) => {
    var rowCount = event.api.getSelectedNodes().length;
    window.alert("selection changed, " + rowCount + " rows selected");
  },
  [window],
);



 const onCellValueeChanged = useCallback((event) => {
  console.log(event); // access the entire event object
  console.log(event.data.Name); // access and print the updated row data
 // const gridData = getAllData(); // Accessing getAllData function
}, []);


// defining the table rows and columns
const [rowData, setRowData] = useState([]);

const [colDefs, setColDefs] = useState([

  // column 1
  { headerName: "Name", field: "Name", 
    headerCheckboxSelection: true,
    checkboxSelection: true,
},

  //column 2
  { headerName: "Status", field: "Status", 
  // continue form here
  // implement the status logic form here
    cellRenderer: StatusRenderer
  },
  { headerName: "Registration ID", field: "Reg_Id" },
  { headerName: "Attendance %", 
    field: "percentage", 
 //  valueFormatter: params => { return params.value.toLocaleString() + " %"; },
    flex: 1,
    cellStyle: params => {
      if (params.value == 100) {
        return { color: 'green' };
      } else if (params.value <= 75) {
        return { color: 'red' };
      } else {
        return { color: 'black' };
      }
    }
   ,cellRenderer: p => <> {p.value}<span style={{ color: 'rgb(184, 184, 184)' }}>%</span>
   </>
  },
  { headerName: "Semester", field: "semester"},
  
  { headerName: "Request Status",field: "reqStatus", cellRenderer: ReqStatusRenderer
},
]);

// defuault column styling
const defaultColDef = {
    flex:1,
//    resizable: false,
    editable: false,
    sortable: true,
    filter: true,
    lockPosition: true,
    headerCheckboxSelectionFilteredOnly: true
};

// getting the data from the server
useEffect(() => {
  console.log('Fetching table list from server 1');
  fetch('http://localhost:3000/dashboard')
    .then(result => result.json())
    //.then(rowData => console.log(rowData)); // Update state of `rowData`
    .then(rowData => setRowData(rowData));
 }, [])

  return (
    <div className="ag-theme-quartz" >
    {/*  <button onClick={CustomButtonComp}>this button</button> */ }
   <AgGridReact

        defaultColDef={defaultColDef}
        columnDefs={colDefs}
        rowData={rowData}
        //pagination={true}
      //  onCellClicked={onCellClicked}
        suppressRowClickSelection="true"
        class="ag-theme-balham"
        gridReady="onGridReady($event)"
        selectionChanged="onSelectionChanged()"
        rowSelection="multiple"
        onGridReady="onGridReady"
        onRowSelected= {onRowSelected}
        //onSelectionChanged= {onSelectionChanged}
    //    onSelectionChanged= {onCellValueChanged}
        onCellValueChanged = {onCellValueeChanged}
   />
    </div>
  )
}

//export default portal









/////////////////////////

  // const exportTableAsCsv = () => {
  //   const selectedRows = gridRef.current.api.getSelectedRows();
  //   gridRef.current.api.exportDataAsCsv({ allColumns: true, onlySelected: true, skipHeader: false, fileName: 'export.csv' });
  // };


   /*
  // Method to download CSV
  const exportTableAsCsv = () => {
    // Create a new array to store the data for exporting
    const exportData = [];

    // const columnHeaders = gridRef.current.api.getColumnDefs().map(col => col.headerName);
    // exportData.push(columnHeaders);

    gridRef.current.api.forEachNode(node => {

      const isSelected = node.isSelected() ? 1 : 0;
      
      const th_regId = sessionStorage.getItem('LoggedUserId');
      console.log(th_regId);
      // Get the data for the current row
      const rowData = node.data;
      
      // Add a new property to the row data indicating the selection status
      const rowDataWithSelection = { ...rowData, isSelected , th_regId };
      
      // Add the modified row data to the export data array
      exportData.push(rowDataWithSelection);
    });
  
    // Convert exportData to CSV format
    const csvData = exportData.map(row => Object.values(row).join(',')).join('\n');
    
    // Create a hidden anchor element to trigger the download
    const hiddenElement = document.createElement('a');
    hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csvData);
    hiddenElement.target = '_blank';
    hiddenElement.download = 'export.csv';
    hiddenElement.click();
  };
  */
  //////////////////////////////
  

  const exportTableAsCsv = () => {
    // Create a new array to store the data for exporting
    const exportData = [];
 
    // const columnHeaders = gridRef.current.api.getColumnDefs().map(col => col.headerName);
    // exportData.push(columnHeaders);
    const th_regId = sessionStorage.getItem('LoggedUserId');
 
    var datetime = new Date();
    //console.log(datetime);
    var date = datetime.toISOString().slice(0,10);
    var time = datetime.toISOString().slice(11,19);
 
 
    gridRef.current.api.forEachNode(node => {
       const isSelected = node.isSelected() ? 1 : 0;
       const rowData = node.data;
       const rowDataWithSelection = { ...rowData, isSelected, th_regId, date, time };
       exportData.push(rowDataWithSelection);
     });
 
     // Convert exportData to CSV format
     const csvData = exportData.map(row => Object.values(row).join(',')).join('\n');
     
     // Make a POST request to the server
     fetch('http://localhost:3000/dashboard', {
       method: 'POST',
       headers: {
         'Content-Type': 'text/csv'
       },
       body: csvData
     })
     .then(response => {
       if (!response.ok) {
         throw new Error('Failed to upload CSV data');
       }
       // Handle successful response
       console.log('CSV data uploaded successfully');
     })
     .catch(error => {
       // Handle error
       console.error('Error uploading CSV data:', error);
     });
   };
   

       {/* <select
        name="Expsn" onChange={(event) => { setCourse_id(event.target.value);}}>
        <option value="Null" disabled selected>Select Course</option>
        {expansions.map((expansion) => (
          <option key={expansion.course_id}
            value={expansion.course_id}>
            {expansion.courseIDS}
          </option>
        ))}
        </select>; */}


        {/* <select
          name="Expsn"
          value={course_id}
          onChange={(event) => { setCourse_id(event.target.value); }}
        >
          <option value="Null" disabled>Select Course</option>
          {expansions.map((expansion) => (
            <option key={expansion.course_id} value={expansion.course_id}>
              {expansion.courseIDS}
            </option>
          ))}
        </select>; */}


        {/* <h1>Expansions</h1>

<select
  name="Expsn" onChange={(event) => { setCourse_id(event.target.value);}}>
  <option value="Null" disabled selected>Select Course</option>
  {expansions.map((expansion) => (
    <option key={expansion.course_id}
      value={expansion.course_id}>
      {expansion.courseIDS}
    </option>
  ))}
  </select>;

<div>
<input type="radio" id="Regular" name="att_radio_type" value="Regular" checked={Attendance_type === "Regular"}
onChange={handleRadioChange}/>
<label htmlFor="Regular">Regular</label>
<br/>
<input type="radio" id="Makeup" name="att_radio_type" value="Makeup" checked={Attendance_type === "Makeup"} onChange={handleRadioChange}
/>
<label htmlFor="Makeup">Makeup</label>
</div> */}



//// orignal
  // useEffect(() => {
  //   console.log('Fetching column from server');
  //   fetch('http://localhost:3000/dashboard/nothing')
  //     .then(response => {
  //       if (!response.ok) {
  //         throw new Error('Failed to fetch data');
  //       }
  //       return response.json();
  //     })
  //     .then(data => {
  //       setExpansions(data); // Update the expansions state with the fetched data
  //     })
  //     .catch(error => console.error('Error fetching data:', error));
  // }, []);