import React, { useState, useEffect, useRef } from 'react'


const NewGroup = ({ validateGroupName, className, setCreatingNewGroup, createNewGroup }) => {
   const [width, setWidth] = useState(0);
   const span = useRef();
   const [groupName, setGroupName] = useState('');

   useEffect(() => {
      setGroupName('');
   }, [])

   useEffect(() => {
      if (span.current.offsetWidth !== 0)
         setWidth(span.current.offsetWidth);
      else setWidth('90px');
   }, [groupName]);

   const handleSaveBtnClick = (e) => {
      const groupNameValidationResult = validateGroupName('****', groupName);
      if (groupNameValidationResult) {
         createNewGroup(groupName);
         setCreatingNewGroup(false);
      }
   }

   const handleOnBlur = (e) => {
      const groupNameValidationResult = validateGroupName('****', groupName);
      if (groupNameValidationResult) { createNewGroup(groupName); setCreatingNewGroup(false) }
      if(groupName === '') setCreatingNewGroup(false);
   }

   const handleKeyDown = (e) => {
      if (e.key === 'Enter') {
         const groupNameValidationResult = validateGroupName('****', groupName);
         if (groupNameValidationResult) {
            createNewGroup(groupName);
            setCreatingNewGroup(false);
         }
      }
      if (e.key === 'Escape') {
         setCreatingNewGroup(false);
      }
   }


   return (
      <div
         className={"rounded-xl shadow-lg w-[240px] border-t border-x overflow-hidden h-fit relative" + ' ' + className}>
         <span id="hide" ref={span} className='text-lg font-semibold absolute z-0 -translate-x-[100%] opacity-0'>{groupName}</span>
         <div className="flex px-6 border-b border-b-gray-300 bg-gray-200 pt-3 transition-colors">
            <input
               id='new-group-name-input'
               type="text"
               placeholder='NewGroup'
               value={groupName}
               onBlur={handleOnBlur}
               onKeyDown={handleKeyDown}
               onChange={e => setGroupName(e.target.value)}
               className={" text-lg font-semibold text-gray-700 bg-inherit focus:outline-none placeholder:text-gray-700/25"}
               style={{ width }}
               autoFocus />
            <button
               className='text-sm rounded-md text-gray-500 transition-all mx-1 px-1 active:bg-slate-100'
               onMouseDown={handleSaveBtnClick}
            >save
            </button>
         </div>
         <div className="bg-inherit h-10 text-center text-gray-500 my-auto flex items-center cursor-pointer hover:bg-gray-100"><span className="w-full">CANCEL</span></div>
      </div>
   )
}

export default NewGroup