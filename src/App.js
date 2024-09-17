import { useState, useEffect, useRef } from 'react';
import userSvg from './img/user.svg'
import groupSvg from './img/group.svg'
import InputMask from 'react-input-mask';
import AlertSvg from './img/AlertSvg';
import { v4 as uuidv4} from 'uuid';
import axios from 'axios';
import Group from './components/Group';
import NewGroup from './components/NewGroup';
import HistoryBar from './components/HistoryBar';

function App() {
   const addGroupButton = useRef(null);
   const groupSelectorRef = useRef(null);

   const [smsText, setSmsText] = useState('');
   const [textareaClasses, setTextareaClasses] = useState('');
   const [stopSend, setStopSend] = useState(false);
   const [selectionContainerClasses, setSelectionContainerClasses] = useState('');
   const [smsHeaderClasses, setSmsHeaderClasses] = useState('');
   const [newNum, setNewNum] = useState('');
   const [showNewNumInput, setShowNewNumInput] = useState(false);
   const [showGroupSelector, setShowGroupSelector] = useState(false);
   const [groups, setGroups] = useState(localStorage.getItem('groupsLocalCache') 
      ? JSON.parse(localStorage.getItem('groupsLocalCache'))
      : []);
   const [smsHeader, setSmsHeader] = useState(localStorage.getItem('smsHeader')
      ? localStorage.getItem('smsHeader')
      : 'test');
   const [selection, setSelection] = useState([]);
   const [globalEdit, setGlobalEdit] = useState('');
   const [sendingProgress, setSendingProgress] = useState(0);
   const [globalErrorGroup, setGlobalErrorGroup] = useState('');
   const [creatingNewGroup, setCreatingNewGroup] = useState(false);
   const [appSettings, setAppSettings] = useState({
      ignoreInvalidNumbers: true
   })

   useEffect(() => {
      if (showNewNumInput) {
         // Фокусируем поле ввода, когда showNewNumInput установлено в true
         document.getElementById('new-num-input')?.focus();
      }
   }, [showNewNumInput]);

   useEffect(() => {
      const handleClickOutside = (event) => {
         if (
            groupSelectorRef.current &&
            !groupSelectorRef.current.contains(event.target) &&
            addGroupButton.current &&
            !addGroupButton.current.contains(event.target)
         ) {
            setShowGroupSelector(false);
         }
      };
      if (showGroupSelector) {
         setPosition();
         window.addEventListener('click', handleClickOutside);
      }
      return () => {
         window.removeEventListener('click', handleClickOutside);
      };
   }, [showGroupSelector]);

   useEffect(() => {
      if (globalErrorGroup !== '') {
         setTimeout(()=>setGlobalErrorGroup(''), 700)
      }
   }, [globalErrorGroup]);

   useEffect(() => {
      localStorage.setItem('groupsLocalCache', JSON.stringify(groups));
   }, [groups]);

   const handleSmsHeaderChange = (e) => {
      setSmsHeader(e.target.value);
      localStorage.setItem('smsHeader', e.target.value); 
   }

   const calculatePosition = () => {
      const buttonRect = addGroupButton.current.getBoundingClientRect();
      const selectorHeight = groupSelectorRef.current.clientHeight;
      const top = buttonRect.bottom + window.scrollY;
      const left = buttonRect.right + window.scrollX;
      if (buttonRect.bottom < selectorHeight) {
         return { top: window.scrollY, left };
      }
      return { top: top - selectorHeight, left };
   };

   const setPosition = () => {
      const { top, left } = calculatePosition();
      const groupSelector = groupSelectorRef.current;

      if (groupSelector) {
         groupSelector.style.position = 'absolute';
         groupSelector.style.top = `${top}px`;
         groupSelector.style.left = `${left}px`;
      }
   };

   const checkNewNumAndSave = (inputValue) => {
      inputValue = inputValue.replaceAll('-_', '');
      inputValue = inputValue.replaceAll('_', '');
      const pattern = /^\+992 \d{3}-\d{2}-\d{2}-\d{2}$/;
      if (pattern.test(inputValue)) {
         setSelection([
            ...selection,
            {
               id: uuidv4(),
               mode: 'number',
               label: inputValue
            }])
      }
      else if (inputValue !== '') {
         setSelection([
            ...selection,
            {
               id: uuidv4(),
               mode: 'invalid',
               label: inputValue
            }])
      }
      setNewNum('');
   }

   const saveNewNumOnEnter = (e) => {
      let inputValue = e.target.value;
      if (e.keyCode === 13) {
         checkNewNumAndSave(inputValue);
         setShowNewNumInput(false);
      }
   }

   const saveNewNumOnBlur = (e) => {
      let inputValue = e.target.value;
      checkNewNumAndSave(inputValue);
      setShowNewNumInput(false);
   }

   const clearInputAndClose = (e) => {
      setShowNewNumInput(false);
      setNewNum('');
   }

   const deleteFromSelection = (id) => {
      setSelection(selection.filter(s => s.id !== id));
   }

   const addGroupToSelection = (groupId) => {
      setSelection([...selection, {
         id: uuidv4(),
         mode: 'group',
         label: groupId
      }])
      setShowGroupSelector(false);
   }

   async function sendQueriesSequentially(sendingQuery) {
      const sessionId = uuidv4();
      const totalMessages = sendingQuery.length;
      let i = 1;
      for (const q of sendingQuery) {
         try {
            // const response = await axios.post('http://localhost/sms_processor.php', {
            const response = await axios.post('./sms_processor.php', {
            // const response = await axios.post('http://cm75198.tw1.ru/sms_processor.php', {
               session_id: sessionId,
               from: smsHeader,
               to: q.number,
               content: smsText
            },
               {
                  headers: {
                     'Content-Type': 'application/json; charset=utf-8',
                  },
               });
            console.log(response.data);
         } catch (error) {
            console.log('Ошибка при отправке запроса', error);
         }
         finally{
            setSendingProgress(i/totalMessages * 100);
            i++;
            console.log(sendingProgress);
         }
      }
      setSendingProgress(0);
      setStopSend(false);
   }


   const handleSendClick = async () => {
      const errorClasses = 'ring-2 ring-red-400 animate-wiggle ';
      if (smsText === '' || selection.length === 0 || smsHeader === '') {
         if (smsText === '') {
            setTextareaClasses(errorClasses);
            setTimeout(() => {
               setTextareaClasses('transition-all');
            }, 700);
         }

         if (selection.length === 0) {
            setSelectionContainerClasses(errorClasses);
            setTimeout(() => {
               setSelectionContainerClasses('transition-all');
            }, 700);
         }

         if (smsHeader === '') {
            setSmsHeaderClasses(errorClasses);
            setTimeout(() => {
               setSmsHeaderClasses('transition-all');
            }, 700);
         }
      }
      else {
         setStopSend(true);
         const sendingQuery = [];
         const unique = [];
         const _selection = sortArr(selection);
         // console.log(_selection);
         _selection.forEach(s => {
            if (s.mode === 'group') {
               const group = groups.find(g => g.groupId === s.label)
               group.numbers.forEach(n => {
                  if (!unique.includes(n)){
                     sendingQuery.push({
                        number: n.replace(/\D/g, "").replace(/^992/, ""),
                        groupId: group.groupId
                     })
                     unique.push(n)
                  }
                  
               })
            }
            else if (s.mode === 'number' && !unique.includes(s.label)) {
               sendingQuery.push({
                  number: s.label.replace(/\D/g, "").replace(/^992/, ""),
                  groupId: null
               })
               unique.push(s.label);
            }
            else if (s.mode === 'invalid' && !appSettings.ignoreInvalidNumbers && !unique.includes(s.label)) {
               sendingQuery.push({
                  number: s.label.replace(/\D/g, "").replace(/^992/, ""),
                  groupId: null
               });
               unique.push(s.label);
            }
         })
         sendQueriesSequentially(sendingQuery);
      }
   }

   const sortArr = (inputArray) => {
      return inputArray.sort((a, b) => {
         if (a.mode === 'group') {
            return -1;
         }
         if (b.mode === 'group') {
            return 1;
         }
         return 0;
      });

   };

   const setGroupById = (index, newData) => {
      setGroups([...groups.slice(0, index), newData, ...groups.slice(index+1)]);
   }
   const createGroupSetterByIndex = (index) => {
      return (newData) => {
         setGroupById(index, newData);
      }
   };

   const validateGroupName = (oldName, newName) => {
      if (oldName === newName) return true;
      else if (newName === '') {
         if (oldName === '****') {
            setGlobalErrorGroup(oldName);
            return false;
         }
         setGlobalErrorGroup(newName);
         return false;
      }
      else if (groups.map(g => g.groupId).includes(newName)){
         setGlobalErrorGroup(newName);
         return false;
      }
      else return true;
   }

   const createNewGroup = (name) => {
      setGroups( [{
         groupId: name,
         numbers: [],
         },
         ...groups]
      )
   }

   const deleteGroup = (name) => {
      return function() {setGroups(groups.filter(g=> g.groupId !== name))}
   }



   return (<>
   {
      <HistoryBar/>
   }
      <main className="container mx-auto flex flex-col items-center bg-[414243]">
         {/* <h2 className="text-center text-4xl font-semibold uppercase text-gray-400 py-6 border-b-2 border-b-blue-200/50">super-puper smart sms sender</h2> */}
         {/* <h2 className="text-center text-2xl font-semibold uppercase text-gray-400 py-4 border-b-2 border-b-blue-200/50">super-puper smart sms sender</h2> */}

         <div className="w-full mt-4">
            {/* <section id="groups" className="flex space-x-6 columns-3 flex-wrap"> */}
            <section id="groups" className="columns-1 sm:columns-2 md:columns-3 lg:columns-4 xl:columns-5 2xl:columns-6 gap-4 space-y-6">
               <div className="flex justify-center w-[240px]">
                  <button
                     className="rounded-xl shadow-lg w-36 h-48 border-t border-x overflow-hidden flex flex-col justify-center items-center hover:bg-gray-50/50 active:scale-90 "
                     onClick={e=> {setCreatingNewGroup(!creatingNewGroup)}}>
                     <div className="plus__custom relative aspect-square w-20 mb-3">
                        <div className="rounded-full w-full h-1 bg-gray-300 absolute top-1/2 -translate-y-1/2"></div>
                        <div className="rounded-full h-full w-1 bg-gray-300 absolute left-1/2 -translate-x-1/2"></div>
                     </div>
                     <h5 className="text-center text-lg text-gray-400">add group</h5>
                  </button>
               </div>
               {creatingNewGroup &&
                  <NewGroup 
                  setCreatingNewGroup = {setCreatingNewGroup}
                  createNewGroup={createNewGroup}
                  validateGroupName={validateGroupName}
                  className={globalErrorGroup === '****' ? 'transition-all animate-wiggle ring-2 ring-red-400 bg-red-100/25' : ''} 
                  />}
               {
                  groups.map((group, index) =>
                     <Group 
                        validateGroupName={validateGroupName}
                        group={group} 
                        className = {globalErrorGroup === group.groupId ? 'transition-all animate-wiggle ring-2 ring-red-400 bg-red-100/25' : ''} 
                        setGroup={createGroupSetterByIndex(index)} 
                        deleteGroup = {deleteGroup(group.groupId)}
                        key={group.groupId}/>
                  )
               }

            </section>
            <section id="sender" className="flex flex-col md:flex-row mt-12 items-center space-x-5 h-[280px] justify-center ">
               <div className='flex h-full space-x-5 relative'>
                  {stopSend && <div className='absolute top-0 left-0 w-full h-full opacity-0 cursor-wait rounded-xl'></div>}
                  <div className={"rounded-xl shadow-lg w-[250px] border-t border-x overflow-y-auto text-gray-800 " + selectionContainerClasses + (stopSend && ' bg-black/[3%]')}>
                     {selection.map(s =>
                        <div className={"py-1 border-b text-center flex px-2  " + (s.mode === 'invalid' ? 'bg-red-100' : '')} key={s.id}>
                           {s.mode === 'invalid'
                              ? <div title='Номер скорее всего неверен'><AlertSvg className='w-6 fill-red-600 ' /></div>
                              : <img src={s.mode === 'group' ? groupSvg : userSvg} alt="ico" className="w-6" />
                           }
                           <div className=' w-full text-center overflow-hidden'><span className='translate-x-6 ' title={s.label}>{s.label}</span></div>
                           <div
                              onClick={e => deleteFromSelection(s.id)}
                              className="exit__custom relative aspect-square w-4 h-4 mt-1 cursor-pointer hover:scale-[1.17] active:scale-90">
                              <div className="rounded-full w-full h-[3px] bg-gray-900 absolute top-1/2 -translate-y-1/2 rotate-45"></div>
                              <div className="rounded-full h-full w-[3px] bg-gray-900 absolute left-1/2 -translate-x-1/2 rotate-45"></div>
                           </div>
                        </div>)}
                     <div id='add-number-to-selection' className={'flex border-b px-2 py-1 flex-between  ' + (showNewNumInput ? '' : 'hidden')} >
                        +<img src={userSvg} alt="ico" className="w-6" />
                        <InputMask
                           id='new-num-input'
                           mask="+\9\92 999-99-99-99"
                           maskChar="_"
                           onChange={(e) => setNewNum(e.target.value)}
                           onBlur={saveNewNumOnBlur}
                           onKeyDown={saveNewNumOnEnter}
                           className='text-center w-full focus:outline-none bg-inherit'
                           value={newNum}
                        />
                        {/* <input type="text" className='text-center w-full focus:outline-none '/> */}
                        <div
                           className="exit__custom relative aspect-square w-4 h-4 mt-1 cursor-pointer hover:scale-110 active:scale-90"
                           onMouseDown={(e) => clearInputAndClose(e)}>
                           <div className="rounded-full w-full h-[3px] bg-gray-900 absolute top-1/2 -translate-y-1/2 rotate-45"></div>
                           <div className="rounded-full h-full w-[3px] bg-gray-900 absolute left-1/2 -translate-x-1/2 rotate-45"></div>
                        </div>
                     </div>
                     <div className="flex border-b ">
                        <button
                           className="py-1 w-1/2 border-r flex justify-center btn-in-text_colors"
                           title="add number"
                           onClick={(e) => { setShowNewNumInput(true); }}>
                           +<img src={userSvg} alt="add number" className="w-6" />
                        </button>
                        <button
                           className="py-1 w-1/2 flex justify-center btn-in-text_colors "
                           title="add group"
                           onClick={(e) => { setShowGroupSelector(!showGroupSelector); }}
                           ref={addGroupButton}>
                           +<img src={groupSvg} alt="add group" className="w-6" />
                        </button>
                     </div>
                  </div>
                  <div className='w-[450px] lg:w-[585px] xl:w-[768px] h-full flex flex-col'>
                     <div className={"rounded-xl border shadow-lg w-full flex items-center mb-3 " + smsHeaderClasses + (stopSend && ' bg-black/[3%]')}>
                        <span className='text-gray-400 ml-4'>From: </span>
                        <input 
                           type="text" 
                           disabled={stopSend}
                           value={smsHeader}
                           onChange={handleSmsHeaderChange}
                           className=' focus:outline-none p-2 w-full rounded-xl focus:text-gray-800 text-gray-500'/>
                     </div>
                     <textarea
                        name="sms-text"
                        id="sms-text"
                        // cols="100"
                        // rows="10"
                        value={smsText}
                        disabled={stopSend}
                        onChange={e => setSmsText(e.target.value)}
                        className={"rounded-xl border focus:outline-gray-300 p-2 shadow-lg w-full flex-grow resize-none disabled:bg-black/[3%] " + textareaClasses}
                        placeholder="Message Text"></textarea>
                  </div>
               </div>
               <button
                  disabled={stopSend}
                  className="rounded-xl py-2 bg-sky-500 text-white w-full 
                        md:min-w-[107px] md:w-[auto] md:h-36 lg:h-56 text-2xl lg:min-w-[129px]  xl:min-w-[156px]
                        active:scale-90 active:bg-sky-600
                         disabled:cursor-wait transition-[background]"
                  style={stopSend ? { background: `linear-gradient(0deg, rgb(14,165,233) ${sendingProgress}%, rgba(14,165,233, 0.25) ${sendingProgress}%)`} : null}
                  onClick={handleSendClick}>{stopSend ? "SENDING":"SEND"}</button>
            </section>
            <section id="status" >
            </section>
         </div>
      </main>
      <footer className='min-h-16'>
         
      </footer>
      {showGroupSelector &&
         <div
            className="shadow-md bg-white rounded-md border "
            id='group-selector'
            ref={groupSelectorRef}>
            {groups.filter(d => !selection.some((selectedItem) => selectedItem.label === d.groupId)).length !== 0
               ? groups.filter(d => !selection.some((selectedItem) => selectedItem.label === d.groupId)).map(d =>
                  <p key={d.groupId}
                     className='border-b py-1 px-4 cursor-pointer hover:bg-gray-100 active:bg-gray-200 text-gray-700'
                     title={'количество номеров: ' + d.numbers.length}
                     onClick={(e) => addGroupToSelection(d.groupId)}
                  >{d.groupId} ({d.numbers.length})
                  </p>)
               : <p
                  className='border-b py-1 px-4 cursor-not-allowed hover:bg-gray-100 active:bg-gray-200 text-gray-300 text-center'
                  title='пусто'
                  onClick={(e) => setShowGroupSelector(false)}
               >(пусто)
               </p>
            }
         </div>}
   </>
   );
}

export default App;
